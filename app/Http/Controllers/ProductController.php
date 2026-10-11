<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search, category filtering,
     * stock status filtering, allowlist sorting, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'suppliers']);

        // 1. Search by SKU or Name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Category filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 3. Stock status filter
        $stockStatus = $request->input('stock_status', 'all');
        if ($stockStatus === 'low') {
            $query->whereColumn('stock', '<=', 'minimum_stock')->where('stock', '>', 0);
        } elseif ($stockStatus === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($stockStatus === 'available') {
            $query->whereColumn('stock', '>', 'minimum_stock');
        }

        // 4. Safe Sorting with Allowlist
        $allowedSorts = ['name', 'sku', 'price', 'stock', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'created_at';
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $products = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sort', 'direction', 'stockStatus'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created product in storage with atomic transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['exists:suppliers,id'],
            'supplier_prices' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'sku' => strtoupper(trim($validated['sku'])),
                'name' => trim($validated['name']),
                'category_id' => $validated['category_id'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'minimum_stock' => $validated['minimum_stock'],
            ]);

            // Sync supplier relationships with pivot prices
            if ($request->filled('suppliers')) {
                $pivotData = [];
                foreach ($request->input('suppliers') as $supplierId) {
                    $supplierPrice = $request->input("supplier_prices.{$supplierId}") ?? $validated['price'];
                    $pivotData[$supplierId] = [
                        'supplier_price' => $supplierPrice > 0 ? $supplierPrice : $validated['price'],
                    ];
                }
                $product->suppliers()->sync($pivotData);
            }

            // Create initial stock movement log if stock > 0
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id() ?? 1,
                    'type' => 'IN',
                    'quantity' => $product->stock,
                    'notes' => 'Stok awal pembuatan produk baru',
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan.');
    }

    /**
     * Display the specified product with detailed associations.
     */
    public function show(Product $product): View
    {
        $product->load([
            'category',
            'suppliers',
            'stockMovements' => function ($q) {
                $q->with('user')->latest('id')->take(15);
            },
        ]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $product->load('suppliers');

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['exists:suppliers,id'],
            'supplier_prices' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($product, $validated, $request) {
            $product->update([
                'sku' => strtoupper(trim($validated['sku'])),
                'name' => trim($validated['name']),
                'category_id' => $validated['category_id'],
                'price' => $validated['price'],
                'minimum_stock' => $validated['minimum_stock'],
            ]);

            // Sync suppliers
            $pivotData = [];
            if ($request->filled('suppliers')) {
                foreach ($request->input('suppliers') as $supplierId) {
                    $supplierPrice = $request->input("supplier_prices.{$supplierId}") ?? $validated['price'];
                    $pivotData[$supplierId] = [
                        'supplier_price' => $supplierPrice > 0 ? $supplierPrice : $validated['price'],
                    ];
                }
            }
            $product->suppliers()->sync($pivotData);
        });

        return redirect()->route('products.show', $product)->with('success', 'Informasi produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product with referential integrity protection.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Check if product has stock movements history to preserve audit logs
        if ($product->stockMovements()->exists()) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena memiliki riwayat pergerakan stok. Data audit inventaris harus tetap terjaga.");
        }

        DB::transaction(function () use ($product) {
            $product->suppliers()->detach();
            $product->delete();
        });

        return redirect()->route('products.index')->with('success', "Produk '{$product->name}' berhasil dihapus.");
    }
}
