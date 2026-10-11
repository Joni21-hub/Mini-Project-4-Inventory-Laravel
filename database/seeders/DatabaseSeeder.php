<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (Admin & Staff)
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Utama',
                'email' => 'admin@inventory.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Gedung IT Lantai 3, Jakarta',
            ]
        );

        $staff = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Ahmad Fauzi (Staff Gudang)',
                'email' => 'staff@inventory.test',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'phone' => '089876543210',
                'address' => 'Komplek Pergudangan Blok B, Jakarta',
            ]
        );

        // 2. Kategori Produk
        $categoriesData = [
            ['name' => 'Perangkat Komputer & Laptop', 'description' => 'Komputer desktop, laptop, monitor, dan periferal utama'],
            ['name' => 'Aksesoris & Input Device', 'description' => 'Keyboard, mouse, webcam, headset, dan aksesoris lainnya'],
            ['name' => 'Perangkat Jaringan (Networking)', 'description' => 'Router, switch hub, kabel LAN, dan access point'],
            ['name' => 'Penyimpanan Data (Storage)', 'description' => 'SSD, harddisk eksternal, flashdisk, dan micro SD'],
            ['name' => 'Perlengkapan Kantor', 'description' => 'Kertas HVS, tinta printer, binder, dan alat tulis kantor'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[] = Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Mitra Supplier
        $suppliersData = [
            ['name' => 'PT Nusantara Komputindo', 'contact_name' => 'Budi Santoso', 'phone' => '021-5551234', 'email' => 'budi@nusantaratelekom.co.id', 'address' => 'Jl. Mangga Dua Raya No. 45, Jakarta Pusat'],
            ['name' => 'CV Mega Tech Distribusi', 'contact_name' => 'Siti Aminah', 'phone' => '021-5555678', 'email' => 'siti@megatechdist.com', 'address' => 'Ruko Harco Mangga Dua Blok A/12, Jakarta Pusat'],
            ['name' => 'PT Global Solusi Digital', 'contact_name' => 'Hendra Wijaya', 'phone' => '031-7788990', 'email' => 'hendra@globalsolusi.co.id', 'address' => 'Jl. Basuki Rahmat No. 88, Surabaya'],
            ['name' => 'CV Sumber Logistik Mandiri', 'contact_name' => 'Rian Pratama', 'phone' => '022-4455667', 'email' => 'rian@sumberlogistik.co.id', 'address' => 'Kawasan Industri Cimahi No. 10, Bandung'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Produk Master
        $productsData = [
            [
                'category_id' => $categories[0]->id,
                'sku' => 'PRD-LPT-001',
                'name' => 'Laptop Lenovo ThinkPad T14 Gen 3',
                'price' => 14500000.00,
                'stock' => 8,
                'minimum_stock' => 3,
            ],
            [
                'category_id' => $categories[0]->id,
                'sku' => 'PRD-MON-002',
                'name' => 'Monitor LG 24 Inch UltraGear FHD IPS',
                'price' => 2100000.00,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories[1]->id,
                'sku' => 'PRD-KBD-003',
                'name' => 'Keyboard Mechanical Keychron K2 Wireless',
                'price' => 1250000.00,
                'stock' => 12,
                'minimum_stock' => 4,
            ],
            [
                'category_id' => $categories[1]->id,
                'sku' => 'PRD-MOU-004',
                'name' => 'Mouse Logitech MX Master 3S Darkfield',
                'price' => 1450000.00,
                'stock' => 4, // Menipis
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories[2]->id,
                'sku' => 'PRD-RTR-005',
                'name' => 'Router MikroTik RB750Gr3 hEX Gigabit',
                'price' => 875000.00,
                'stock' => 20,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories[2]->id,
                'sku' => 'PRD-SWT-006',
                'name' => 'Switch TP-Link Gigabit 16-Port SG1016D',
                'price' => 950000.00,
                'stock' => 6,
                'minimum_stock' => 3,
            ],
            [
                'category_id' => $categories[3]->id,
                'sku' => 'PRD-SSD-007',
                'name' => 'SSD Samsung 970 EVO Plus NVMe M.2 1TB',
                'price' => 1650000.00,
                'stock' => 2, // Menipis
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories[3]->id,
                'sku' => 'PRD-SSD-008',
                'name' => 'SSD Kingston NV2 PCIe 4.0 NVMe 500GB',
                'price' => 620000.00,
                'stock' => 25,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories[1]->id,
                'sku' => 'PRD-WBC-009',
                'name' => 'Webcam Logitech C922 Pro Stream HD 1080P',
                'price' => 1350000.00,
                'stock' => 0, // Habis
                'minimum_stock' => 3,
            ],
            [
                'category_id' => $categories[4]->id,
                'sku' => 'PRD-INK-010',
                'name' => 'Tinta Printer Epson 003 Black 65ml',
                'price' => 85000.00,
                'stock' => 50,
                'minimum_stock' => 15,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $createdProducts[] = Product::firstOrCreate(['sku' => $pData['sku']], $pData);
        }

        // 5. Junction Many-to-Many Product & Suppliers (dengan harga beli)
        $createdProducts[0]->suppliers()->syncWithoutDetaching([
            $suppliers[0]->id => ['supplier_price' => 13000000.00],
            $suppliers[2]->id => ['supplier_price' => 13200000.00],
        ]);
        $createdProducts[1]->suppliers()->syncWithoutDetaching([
            $suppliers[0]->id => ['supplier_price' => 1850000.00],
            $suppliers[1]->id => ['supplier_price' => 1800000.00],
        ]);
        $createdProducts[2]->suppliers()->syncWithoutDetaching([
            $suppliers[1]->id => ['supplier_price' => 1050000.00],
        ]);
        $createdProducts[3]->suppliers()->syncWithoutDetaching([
            $suppliers[1]->id => ['supplier_price' => 1200000.00],
        ]);
        $createdProducts[4]->suppliers()->syncWithoutDetaching([
            $suppliers[2]->id => ['supplier_price' => 750000.00],
        ]);
        $createdProducts[6]->suppliers()->syncWithoutDetaching([
            $suppliers[0]->id => ['supplier_price' => 1400000.00],
            $suppliers[1]->id => ['supplier_price' => 1380000.00],
        ]);
        $createdProducts[9]->suppliers()->syncWithoutDetaching([
            $suppliers[3]->id => ['supplier_price' => 68000.00],
        ]);

        // 6. Riwayat Transaksi Mutasi Stok
        $movements = [
            ['product_id' => $createdProducts[0]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 10, 'notes' => 'Pengadaan stok awal peresmian cabang'],
            ['product_id' => $createdProducts[0]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 2, 'notes' => 'Pengeluaran untuk divisi Operasional'],
            ['product_id' => $createdProducts[1]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 20, 'notes' => 'Pengadaan monitor stok baru'],
            ['product_id' => $createdProducts[1]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 5, 'notes' => 'Pengiriman batch unit meja kerja tim frontend'],
            ['product_id' => $createdProducts[2]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 15, 'notes' => 'Restock dari CV Mega Tech'],
            ['product_id' => $createdProducts[2]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 3, 'notes' => 'Kebutuhan tim teknis'],
            ['product_id' => $createdProducts[3]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 10, 'notes' => 'Restock awal mouse'],
            ['product_id' => $createdProducts[3]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 6, 'notes' => 'Distribusi divisi kreatif & design'],
            ['product_id' => $createdProducts[6]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 10, 'notes' => 'Pembelian SSD Samsung dari PT Nusantara'],
            ['product_id' => $createdProducts[6]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 8, 'notes' => 'Upgrade SSD workstation kantor'],
            ['product_id' => $createdProducts[9]->id, 'user_id' => $admin->id, 'type' => 'IN', 'quantity' => 60, 'notes' => 'Stok rutin tinta printer Epson'],
            ['product_id' => $createdProducts[9]->id, 'user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 10, 'notes' => 'Pengisian tinta printer lantai 1 & 2'],
        ];

        foreach ($movements as $m) {
            StockMovement::create($m);
        }
    }
}
