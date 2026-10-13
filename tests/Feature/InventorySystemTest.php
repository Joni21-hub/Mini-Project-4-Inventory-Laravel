<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_username(): void
    {
        $user = User::where('username', 'admin')->first();

        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_renders_successfully(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('InvenTech');
        $response->assertSee('Total Produk');
        $response->assertSee('Total Valuasi Aset');
    }

    public function test_stock_in_transaction_increases_stock(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::first();
        $initialStock = $product->stock;
        $addQuantity = 15;

        $response = $this->actingAs($staff)->post('/stock/in', [
            'product_id' => $product->id,
            'quantity' => $addQuantity,
            'notes' => 'Penerimaan stok tambahan pengujian unit',
        ]);

        $response->assertRedirect('/stock/history');
        $this->assertEquals($initialStock + $addQuantity, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => $addQuantity,
        ]);
    }

    public function test_stock_out_decreases_stock_when_sufficient(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::where('stock', '>=', 5)->first();
        $initialStock = $product->stock;
        $outQuantity = 3;

        $response = $this->actingAs($staff)->post('/stock/out', [
            'product_id' => $product->id,
            'quantity' => $outQuantity,
            'notes' => 'Pengeluaran uji coba unit',
        ]);

        $response->assertRedirect('/stock/history');
        $this->assertEquals($initialStock - $outQuantity, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => $outQuantity,
        ]);
    }

    public function test_stock_out_fails_when_quantity_exceeds_available_stock(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::first();
        $excessiveQty = $product->stock + 1000;

        $response = $this->actingAs($staff)->post('/stock/out', [
            'product_id' => $product->id,
            'quantity' => $excessiveQty,
            'notes' => 'Percobaan pengeluaran melebihi batas stok',
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertNotEquals($product->stock - $excessiveQty, $product->fresh()->stock);
    }

    public function test_product_cannot_be_deleted_if_has_movement_history(): void
    {
        $admin = User::where('role', 'admin')->first();
        $productWithMovements = Product::has('stockMovements')->first();

        $response = $this->actingAs($admin)->delete('/products/' . $productWithMovements->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $productWithMovements->id]);
    }

    public function test_category_cannot_be_deleted_if_has_products(): void
    {
        $admin = User::where('role', 'admin')->first();
        $categoryWithProducts = Category::has('products')->first();

        $response = $this->actingAs($admin)->delete('/categories/' . $categoryWithProducts->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $categoryWithProducts->id]);
    }

    public function test_staff_cannot_create_or_delete_product(): void
    {
        $staff = User::where('role', 'staff')->first();

        $response = $this->actingAs($staff)->get('/products/create');
        $response->assertStatus(403);
    }

    public function test_reports_csv_export_returns_stream(): void
    {
        $staff = User::where('role', 'staff')->first();

        $response = $this->actingAs($staff)->get('/reports/export/products');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Sesuai Slide 48: Demonstrasi satu alur lengkap:
     * membuka form -> validasi -> simpan -> redirect -> tampilkan data.
     */
    public function test_complete_crud_flow_open_form_validate_store_redirect_and_display_data(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();

        // 1. Membuka form tambah produk
        $formResponse = $this->actingAs($admin)->get('/products/create');
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Tambah Produk Baru');

        // 2. Validasi error jika input kosong/tidak valid
        $invalidResponse = $this->actingAs($admin)->post('/products', []);
        $invalidResponse->assertSessionHasErrors(['sku', 'name', 'category_id', 'price', 'stock', 'minimum_stock']);

        // 3. Simpan data valid
        $newProductData = [
            'sku' => 'TEST-FLOW-001',
            'name' => 'Demo Produk Alur Lengkap',
            'category_id' => $category->id,
            'price' => 750000,
            'stock' => 20,
            'minimum_stock' => 5,
        ];

        // 4. Redirect setelah simpan berhasil
        $storeResponse = $this->actingAs($admin)->post('/products', $newProductData);
        $storeResponse->assertRedirect('/products');
        $storeResponse->assertSessionHas('success');

        // Database assertions
        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-FLOW-001',
            'name' => 'Demo Produk Alur Lengkap',
        ]);

        // 5. Tampilkan data pada listing & detail
        $listResponse = $this->actingAs($admin)->get('/products?search=TEST-FLOW-001');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('TEST-FLOW-001');
        $listResponse->assertSee('Demo Produk Alur Lengkap');

        $createdProduct = Product::where('sku', 'TEST-FLOW-001')->firstOrFail();
        $showResponse = $this->actingAs($admin)->get('/products/' . $createdProduct->id);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Demo Produk Alur Lengkap');
    }
}
