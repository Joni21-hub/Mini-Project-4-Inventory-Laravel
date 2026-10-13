# InvenTech — Laravel Inventory Management System
> **Praktikum Pemrograman Web - Pertemuan 5: Laravel Fundamental**  
> Mengubah Sistem Manajemen Inventaris P4 (PHP Native) Menjadi Aplikasi Laravel Berstandar Industri.

---

## 🌟 Highlight Fitur & Keunggulan

1. **Arsitektur Berbasis Laravel 12 & PHP 8.2+**:
   - Struktur rapi mengikuti standar konvensi MVC Laravel.
   - Routing terstruktur dengan Middleware Group (`auth`, `admin`, `guest`).
   - Resource Controllers (`ProductController`, `CategoryController`, `SupplierController`).

2. **Database & Relasi Eloquent ORM**:
   - **One-to-Many**: `Category` hasMany `Product`, `Product` belongsTo `Category`.
   - **Many-to-Many**: `Product` belongsToMany `Supplier` via pivot table `product_suppliers` dengan atribut `supplier_price`.
   - **Audit Trail Mutasi**: `Product` hasMany `StockMovement`, `User` hasMany `StockMovement`.
   - Proteksi Integritas Referensial (*Foreign Key Restrict*): Kategori dengan produk dan produk dengan histori mutasi terlindungi dari penghapusan sembarangan.

3. **Integritas Bisnis Transaksi Stok**:
   - **Atomic Database Transaction (`DB::transaction`)**: Menjamin perubahan stok fisik dan pencatatan log mutasi selalu konsisten atau di-*rollback* secara utuh jika terjadi kegagalan.
   - **Pessimistic Concurrency Locking (`lockForUpdate()`)**: Mencegah kondisi balapan (*race condition*) pada transaksi Barang Keluar (Stock OUT) dan memastikan stok fisik tidak pernah bernilai negatif.

4. **Antarmuka & Pengalaman Pengguna (UI/UX)**:
   - Desain responsif berbasis Bootstrap 5.3 & Icons dengan font Plus Jakarta Sans.
   - Master layout modular (`layouts/app.blade.php`) dengan sidebar navigasi interaktif.
   - **Kalkulator Interaktif Real-Time**: Live preview penambahan stok pada form Barang Masuk dan kalkulator defisit/warning stok habis pada form Barang Keluar sebelum form dikirimkan.
   - **Dashboard Analitik Dinamis**: 5 kartu indikator performa utama, grafik tren keluar/masuk 7 hari (Chart.js Bar), grafik sebaran kategori (Chart.js Donut), tabel peringatan stok kritis, dan 7 transaksi terbaru.

5. **Pelaporan & Ekspor Data**:
   - Filter mutasi berdasarkan rentang tanggal, jenis pergerakan (IN/OUT), dan produk.
   - Ekspor streaming CSV instan untuk **Laporan Mutasi Stok** dan **Laporan Inventaris Produk**.

6. **Pengujian Otomatis (Automated Feature Tests)**:
   - Dilengkapi *suite test* fitur lengkap (`tests/Feature/InventorySystemTest.php`) mencakup alur autentikasi, transaksi stok, validasi defisit, pembatasan hak akses *role*, dan ekspor CSV.
   - **13 Tests / 50 Assertions — 100% Passed**.

---

## 🏛️ Arsitektur MVC & Siklus Request-Response (Slide 10, 11, 47)

Proyek ini menerapkan arsitektur **Model-View-Controller (MVC)** murni sesuai materi Pertemuan 5:

```
 [Client Browser]
        │  (HTTP Request: misal GET /products atau POST /products)
        ▼
 [routes/web.php] ──(Route Match & Middleware: auth, admin)
        │
        ▼
 [Controllers] (misal ProductController, StockMovementController)
        │  • Validasi Input ($request->validate())
        │  • Business Logic & Transaction (DB::transaction, lockForUpdate)
        ▼
 [Models & Eloquent ORM] (Product, Category, Supplier, StockMovement)
        │  • Query Builder / Eager Loading (Product::with('category'))
        │  • Relasi: hasMany, belongsTo, belongsToMany
        ▼
 [Database] (MySQL / MariaDB / SQLite)
        │  • Dikelola via Migrations & Seeders
        ▼
 [Views (Blade Template)] ──(Layout app.blade.php, escaping {{ }}, directives)
        │
        ▼  (HTTP Response: HTML / Redirect with Flash Message / CSV Stream)
 [Client Browser]
```

### Matriks Migrasi P4 (PHP Native) ke P5 (Laravel) (Slide 35)

| Komponen P4 PHP Native | Implementasi P5 Laravel | Lokasi Kode di Proyek |
|---|---|---|
| `database/schema.sql` | Database Migrations | `database/migrations/` (6 file migrasi skema) |
| Form PHP manual | Blade Form + `@csrf` + Validasi | `resources/views/products/create.blade.php` |
| Query PDO manual | Eloquent ORM & Query Builder | `app/Models/Product.php`, `Category.php`, dll. |
| File CRUD terpisah | Resource Controller | `app/Http/Controllers/ProductController.php` |
| Session manual (`$_SESSION`) | Laravel Authentication Layer | `app/Http/Controllers/AuthController.php`, Middleware |
| `include` template | Blade Master Layout | `resources/views/layouts/app.blade.php` |

---

## 🔄 Demonstrasi Alur Lengkap CRUD (Slide 48)

Sesuai instruksi penugasan Slide 48 (*Demonstrasikan satu alur lengkap: membuka form → validasi → simpan → redirect → tampilkan data*):

1. **Membuka Form Tambah Produk**:
   - User Admin mengklik tombol "Tambah Produk Baru" atau mengakses `GET /products/create`.
   - Route mengarahkan ke `ProductController@create` yang mengambil daftar kategori & supplier, lalu mengembalikan view `products.create`.
2. **Validasi Request Input**:
   - User mengirimkan data form via `POST /products`.
   - Controller memverifikasi aturan: SKU unik & wajib, nama wajib, harga >= 0, stok >= 0, kategori valid.
   - Jika data tidak valid, form otomatis kembali dengan pesan error spesifik per kolom (`@error`).
3. **Penyimpanan Transaksional (Atomic DB Transaction)**:
   - Jika valid, `Product::create()` mengeksekusi insert ke tabel `products`.
   - Hubungan ke supplier disinkronkan via pivot table `product_suppliers`.
   - Jika stok awal > 0, sistem otomatis mencatat log mutasi awal (`StockMovement::create`).
4. **Redirect dengan Flash Message**:
   - Controller melakukan `return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan.');`.
5. **Tampilkan Data Terkini**:
   - Halaman index produk me-render produk baru dengan format badge stok, relasi kategori, dan flash alert sukses berwarna hijau.
   - Alur ini juga terverifikasi 100% otomatis di test suite: `InventorySystemTest::test_complete_crud_flow_open_form_validate_store_redirect_and_display_data`.

---

## 📋 Checklist Pemenuhan Modul Pertemuan 5 (Slide 41 & 46)

- [x] **Project Laravel dapat dijalankan**: Berbasis Laravel 12 & PHP 8.2+.
- [x] **.env dan koneksi database benar**: Tersedia `.env` dan `.env.example` (MySQL & SQLite).
- [x] **route:list menampilkan route yang diharapkan**: 39 route terdaftar, RESTful resource routes rapi.
- [x] **Migration dapat dijalankan dari database kosong**: `php artisan migrate:fresh` berhasil tanpa error.
- [x] **Seeder menghasilkan data uji**: Akun admin/staff, kategori, supplier, produk, dan log mutasi.
- [x] **Model memiliki relationship yang benar**: `Category` hasMany `Product`, `Product` belongsToMany `Supplier`, `Product` hasMany `StockMovement`.
- [x] **Resource controller menangani CRUD**: `ProductController` mengimplementasikan 7 aksi CRUD lengkap.
- [x] **Blade memakai layout dan escaping**: Menggunakan `layouts/app.blade.php`, escaping `{{ }}`, layout modular.
- [x] **Form memiliki @csrf dan validasi**: Semua form menyertakan proteksi CSRF token dan validasi field.
- [x] **Search dan pagination mempertahankan query string**: Menggunakan `->withQueryString()`.
- [x] **Fitur & Business Rule Inventory P4 tetap terjaga**: Transaksi IN/OUT, stok tidak boleh negatif (`lockForUpdate`), referential integrity protection.

---

## 🔑 Akun Uji Coba Bawaan (*Seeded Accounts*)

| Peran (Role) | Username | Email | Password | Hak Akses |
|---|---|---|---|---|
| **Administrator** | `admin` | `admin@inventory.test` | `admin123` | Akses penuh CRUD Produk, Kategori, Supplier, Mutasi Stok, Profil, & Laporan |
| **Staff Gudang** | `staff` | `staff@inventory.test` | `staff123` | Transaksi Stok Masuk (IN), Stok Keluar (OUT), Lihat Riwayat, Profil, & Laporan |

*(Tersedia tombol 1-klik Quick Demo Fill pada halaman Login).*

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Prasyarat Sistem
- PHP >= 8.2 (Pastikan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer` aktif di `php.ini`)
- Composer
- MySQL Server (XAMPP / MariaDB aktif di port 3306)

### 2. Konfigurasi Lingkungan
Jika baru mengunduh atau clone repositori:
```bash
# Salin konfigurasi lingkungan
cp .env.example .env

# Generate application key
php artisan key:generate
```

Pastikan pengaturan database di file `.env` mengarah ke database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_laravel
DB_USERNAME=root
DB_PASSWORD=
```
*(Catatan: Bila ingin menggunakan nama database `inventory_db` sesuai Slide 43, cukup sesuaikan `DB_DATABASE=inventory_db`).*

### 3. Migrasi & Seeding Data
Jalankan migrasi tabel beserta *dummy data* lengkap (kategori, supplier, produk, dan log mutasi):
```bash
php artisan migrate:fresh --seed
```

### 4. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui peramban web di: **`http://127.0.0.1:8000`**

### 5. Menjalankan Unit & Feature Test
```bash
php artisan test
```

---

## 📁 Struktur Direktori Kunci

```
Mini-Project-4-Inventory-Laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Login, logout, dan manajemen profil
│   │   │   ├── DashboardController.php     # Perhitungan metrik dan dataset Chart.js
│   │   │   ├── ProductController.php       # Katalog produk, filter, sorting, pagination
│   │   │   ├── CategoryController.php      # Manajemen kategori produk
│   │   │   ├── SupplierController.php      # Manajemen supplier & katalog pasokan
│   │   │   ├── StockMovementController.php # Transaksi IN/OUT dengan Pessimistic Lock
│   │   │   └── ReportController.php        # Valuasi aset & streaming CSV export
│   │   └── Middleware/
│   │       └── AdminMiddleware.php         # Pembatasan akses berbasis peran admin
│   └── Models/
│       ├── User.php
│       ├── Category.php
│       ├── Supplier.php
│       ├── Product.php
│       └── StockMovement.php
├── database/
│   ├── migrations/                         # Migrasi skema database terurut rapi
│   ├── factories/                          # Factory data dummy
│   └── seeders/
│       └── DatabaseSeeder.php              # Akun & sampel inventaris lengkap
├── resources/
│   └── views/
│       ├── layouts/app.blade.php           # Master template Bootstrap 5 & Chart.js
│       ├── auth/                           # Halaman login & profil
│       ├── products/                       # Index, create, edit, show
│       ├── categories/                     # Index, create, edit
│       ├── suppliers/                      # Index, create, edit, show
│       ├── stock/                          # In, out (live preview), history
│       ├── reports/                        # Rekapitulasi & tombol ekspor CSV
│       └── dashboard.blade.php             # Tampilan analitik utama
├── routes/
│   └── web.php                             # Definisi rute aplikasi (RESTful & Middleware)
└── tests/
    └── Feature/
        └── InventorySystemTest.php         # 13 pengujian otomatis menyeluruh
```

---
*Dikembangkan untuk memenuhi standar tugas Pertemuan 5 Praktikum Pemrograman Web.*
