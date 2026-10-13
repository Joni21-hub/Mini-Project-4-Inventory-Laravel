@extends('layouts.app')

@section('title', 'Detail Produk — ' . $product->name)
@section('page_title', 'Informasi Spesifikasi & Persediaan')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Navigation Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border py-1 px-3">
                &larr; Kembali ke Katalog
            </a>
            <span class="text-muted">/</span>
            <span class="badge-sku">{{ $product->sku }}</span>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('stock.in', ['product_id' => $product->id]) }}" class="btn btn-sm btn-success py-1 px-3">
                + Catat Masuk (IN)
            </a>
            <a href="{{ route('stock.out', ['product_id' => $product->id]) }}" class="btn btn-sm btn-danger py-1 px-3">
                - Catat Keluar (OUT)
            </a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                    Edit Produk
                </a>
            @endif
        </div>
    </div>

    <!-- Main Showcase Card with High-Res Photography -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <!-- Left: Real Product Image Showcase -->
                <div class="col-lg-5 text-center">
                    <div class="position-relative d-inline-block w-100" style="max-width: 420px;">
                        <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 320px; background: #f8fafc;">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 class="w-100 h-100 object-fit-cover" style="transition: transform 0.4s ease;">
                        </div>
                    </div>
                </div>

                <!-- Right: Product Specifications & Status -->
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.78rem;">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </span>
                        @if($product->stock <= 0)
                            <span class="status-badge badge-out">
                                <span class="dot"></span> Stok Habis (0)
                            </span>
                        @elseif($product->stock <= $product->minimum_stock)
                            <span class="status-badge badge-low">
                                <span class="dot"></span> Stok Menipis (<= Batas Min)
                            </span>
                        @else
                            <span class="status-badge badge-safe">
                                <span class="dot"></span> Stok Aman & Siap
                            </span>
                        @endif
                    </div>

                    <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.025em;">{{ $product->name }}</h2>
                    <p class="text-muted font-monospace mb-4" style="font-size: 0.85rem;">
                        SKU: {{ $product->sku }} &bull; Terdaftar sejak {{ $product->created_at->format('d M Y') }}
                    </p>

                    <!-- Price & Asset Value Box -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block mb-1">Harga Jual Satuan</span>
                                <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block mb-1">Total Nilai Persediaan Fisik</span>
                                <h3 class="fw-bold text-success mb-0">Rp {{ number_format($product->price * $product->stock, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Gauge & Health Status -->
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold">Status Tingkat Kuantitas Fisik:</span>
                            <span class="fw-bold fs-5 {{ $product->stock <= $product->minimum_stock ? 'text-danger' : 'text-success' }}">
                                {{ number_format($product->stock) }} Unit
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>Batas Minimum Aman: {{ number_format($product->minimum_stock) }} Unit</span>
                            <span>{{ $product->stock > $product->minimum_stock ? 'Persediaan Normal' : 'Wajib Restock Segera' }}</span>
                        </div>

                        @php
                            $ratio = $product->minimum_stock > 0 ? min(100, round(($product->stock / ($product->minimum_stock * 2)) * 100)) : 100;
                            $barClass = $product->stock <= 0 ? 'bg-danger' : ($product->stock <= $product->minimum_stock ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $ratio }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Grids: Supplier Partners & Recent Stock Audit Trail -->
    <div class="row g-4">
        <!-- Supplier Partners -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-dark">Mitra Pemasok Terhubung</h6>
                    <small class="text-muted">Daftar vendor dan referensi harga beli</small>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($product->suppliers as $sup)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('suppliers.show', $sup) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $sup->name }}
                                        </a>
                                        <small class="text-muted d-block">{{ $sup->contact_name }} &bull; {{ $sup->phone }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted small d-block">Harga Beli:</span>
                                        <span class="fw-semibold text-dark">
                                            Rp {{ number_format($sup->pivot->supplier_price ?? $product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">
                                Belum ada mitra supplier yang ditautkan ke produk ini.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Audit Movements for This Product -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Histori Mutasi Produk Ini</h6>
                        <small class="text-muted">15 catatan transaksi keluar/masuk terakhir</small>
                    </div>
                    <a href="{{ route('stock.history', ['product_id' => $product->id]) }}" class="btn btn-sm btn-light border py-1 px-3">
                        Filter Lengkap
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th class="text-center">Kuantitas</th>
                                <th>Keterangan</th>
                                <th>Petugas</th>
                                <th class="text-end">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->stockMovements as $mov)
                                <tr>
                                    <td>
                                        @if($mov->type === 'IN')
                                            <span class="badge-type-in">+ IN</span>
                                        @else
                                            <span class="badge-type-out">- OUT</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold {{ $mov->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                        {{ $mov->type === 'IN' ? '+' : '-' }}{{ $mov->quantity }}
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;">
                                            {{ $mov->notes ?: '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.72rem;">
                                            {{ $mov->user->name ?? 'Sistem' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $mov->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada pergerakan stok untuk produk ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
