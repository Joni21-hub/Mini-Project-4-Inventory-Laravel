@extends('layouts.app')

@section('title', 'Detail Supplier — ' . $supplier->name)
@section('page_title', 'Profil & Katalog Pemasok')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Action Toolbar -->
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-light border py-1 px-3">
            &larr; Kembali ke Daftar Supplier
        </a>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary py-1 px-3 shadow-sm">
                Edit Data Supplier
            </a>
        @endif
    </div>

    <!-- Supplier Info Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-light text-primary border px-2 py-1 mb-2" style="font-size: 0.75rem;">
                        Mitra Vendor Terdaftar
                    </span>
                    <h3 class="fw-bold mb-1 text-dark">{{ $supplier->name }}</h3>
                    <p class="text-muted mb-4">PIC / Narahubung: <strong class="text-dark">{{ $supplier->contact_name ?: 'Tidak tertera' }}</strong></p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block mb-1">Telepon / WhatsApp</span>
                                <span class="fw-bold text-dark fs-6 font-monospace">
                                    {{ $supplier->phone ?: '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block mb-1">Email Korespondensi</span>
                                <span class="fw-bold text-dark fs-6 text-truncate d-block">
                                    {{ $supplier->email ?: '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-4 border rounded-3 bg-light">
                        <h6 class="fw-bold mb-2 text-dark">Alamat Kantor / Pergudangan</h6>
                        <p class="text-muted small mb-0">{{ $supplier->address ?: 'Alamat belum dilengkapi.' }}</p>
                        <hr class="my-3" style="border-color: #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Varian Pasokan:</span>
                            <span class="badge bg-dark text-white px-3 py-1 fs-6">{{ $supplier->products->count() }} Jenis Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog of Supplied Products with Product Thumbnails -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-transparent py-3">
            <h6 class="fw-bold mb-0 text-dark">Katalog Produk Pasokan {{ $supplier->name }}</h6>
            <small class="text-muted">Daftar item yang dipasok oleh vendor ini beserta harga kulakan & margin</small>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th style="width: 70px;">Visual</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Estimasi Margin</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-end px-3">Tindakan</th>
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
                                <div class="product-thumb-container">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                                </div>
                            </td>
                            <td>
                                <span class="badge-sku">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                Rp {{ number_format($buyPrice, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold text-primary">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $margin > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2 py-1">
                                    +Rp {{ number_format($margin, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $product->stock <= $product->minimum_stock ? 'text-warning' : 'text-success' }}">
                                {{ number_format($product->stock) }}
                            </td>
                            <td class="text-end px-3">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.78rem;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Belum ada produk yang ditautkan ke supplier ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
