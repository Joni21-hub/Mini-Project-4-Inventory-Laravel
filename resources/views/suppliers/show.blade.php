@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->name)
@section('page_title', 'Profil & Katalog Pemasok')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Action Toolbar -->
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light border">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Supplier
        </a>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Edit Data Supplier
            </a>
        @endif
    </div>

    <!-- Supplier Info Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 mb-2">
                        Mitra Vendor Terdaftar
                    </span>
                    <h3 class="fw-bold mb-1 text-dark">{{ $supplier->name }}</h3>
                    <p class="text-muted mb-3"><i class="bi bi-person-badge me-1"></i>PIC / Narahubung: <strong>{{ $supplier->contact_name ?: 'Tidak tertera' }}</strong></p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small d-block mb-1">Telepon / WhatsApp</span>
                                <span class="fw-bold text-dark fs-6">
                                    <i class="bi bi-telephone text-primary me-1"></i>{{ $supplier->phone ?: '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small d-block mb-1">Email Korespondensi</span>
                                <span class="fw-bold text-dark fs-6 text-truncate d-block">
                                    <i class="bi bi-envelope text-primary me-1"></i>{{ $supplier->email ?: '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-3 border rounded-3 bg-light">
                        <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt text-danger me-1"></i>Alamat Operasional</h6>
                        <p class="text-muted small mb-0">{{ $supplier->address ?: 'Alamat belum dilengkapi.' }}</p>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Total Varian Pasokan:</span>
                            <span class="badge bg-dark fs-6">{{ $supplier->products->count() }} Jenis Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog of Supplied Products -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Katalog Produk yang Dipasok oleh {{ $supplier->name }}</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Pasokan (Beli)</th>
                        <th class="text-end">Harga Jual Retail</th>
                        <th class="text-end">Margin Keuntungan</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-end px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->products as $product)
                        @php
                            $buyPrice = $product->pivot->supplier_price;
                            $margin = $product->price - $buyPrice;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($buyPrice, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold text-primary">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $margin >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                    Rp {{ number_format($margin, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center fw-bold {{ $product->stock <= $product->minimum_stock ? 'text-danger' : 'text-success' }}">
                                {{ number_format($product->stock) }}
                            </td>
                            <td class="text-end px-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-light border" title="Lihat Detail Produk">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('stock.in', ['product_id' => $product->id]) }}" class="btn btn-light border text-success" title="Pesan Stok Masuk">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada produk yang dikaitkan dengan supplier ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
