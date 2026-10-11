@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)
@section('page_title', 'Informasi Detail Produk')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Navigation & Action Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-light border">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <span class="text-muted">/</span>
            <span class="badge bg-light text-dark font-monospace border">{{ $product->sku }}</span>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('stock.in', ['product_id' => $product->id]) }}" class="btn btn-sm btn-success">
                <i class="bi bi-box-arrow-in-down"></i> Catat Stok Masuk
            </a>
            <a href="{{ route('stock.out', ['product_id' => $product->id]) }}" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-up"></i> Catat Stok Keluar
            </a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i> Edit Produk
                </a>
            @endif
        </div>
    </div>

    <!-- Main Product Stats Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis px-2 py-1">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </span>
                        @if($product->stock <= 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-status">
                                <i class="bi bi-x-circle me-1"></i>Stok Habis
                            </span>
                        @elseif($product->stock <= $product->minimum_stock)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle badge-status">
                                <i class="bi bi-exclamation-triangle me-1"></i>Stok Menipis
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">
                                <i class="bi bi-check-circle me-1"></i>Stok Tersedia
                            </span>
                        @endif
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $product->name }}</h3>
                    <p class="text-muted font-monospace mb-3">SKU: {{ $product->sku }} &bull; Didaftarkan {{ $product->created_at->format('d M Y') }}</p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small d-block mb-1">Harga Jual Satuan</span>
                                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small d-block mb-1">Total Nilai Persediaan</span>
                                <h4 class="fw-bold text-success mb-0">Rp {{ number_format($product->price * $product->stock, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border bg-light">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-3"><i class="bi bi-speedometer2 text-secondary me-2"></i>Status Tingkat Persediaan</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Stok Fisik Tersedia:</span>
                                <span class="fw-bold fs-5 {{ $product->stock <= $product->minimum_stock ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($product->stock) }} unit
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Batas Minimum Aman:</span>
                                <span class="fw-semibold">{{ number_format($product->minimum_stock) }} unit</span>
                            </div>

                            @php
                                $ratio = $product->minimum_stock > 0 ? min(100, round(($product->stock / ($product->minimum_stock * 2)) * 100)) : 100;
                                $barClass = $product->stock <= 0 ? 'bg-danger' : ($product->stock <= $product->minimum_stock ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $ratio }}%;"></div>
                            </div>
                            <small class="text-muted d-block text-end" style="font-size: 0.75rem;">
                                {{ $product->stock > $product->minimum_stock ? 'Persediaan di atas batas aman' : 'Segera lakukan pemesanan ke supplier' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-truck text-primary me-2"></i>Daftar Pemasok Produk Ini (Suppliers)</h6>
            <span class="badge bg-light text-muted border">{{ $product->suppliers->count() }} Pemasok Terhubung</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Supplier</th>
                        <th>Kontak PIC</th>
                        <th>Telepon / Email</th>
                        <th class="text-end">Harga Beli / Pasokan</th>
                        <th class="text-end">Potensi Margin / Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->suppliers as $sup)
                        @php
                            $buyPrice = $sup->pivot->supplier_price;
                            $margin = $product->price - $buyPrice;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('suppliers.show', $sup) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $sup->name }}
                                </a>
                            </td>
                            <td>{{ $sup->contact_name ?? '-' }}</td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i>{{ $sup->phone ?? '-' }}</div>
                                <small class="text-muted">{{ $sup->email ?? '-' }}</small>
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($buyPrice, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $margin >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                    Rp {{ number_format($margin, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada supplier yang ditautkan ke produk ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stock Movement History for this Product -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Pergerakan Stok Produk Ini</h6>
            <a href="{{ route('stock.history', ['product_id' => $product->id]) }}" class="btn btn-sm btn-light border">
                Lihat Seluruh Mutasi
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tipe</th>
                        <th class="text-center">Kuantitas</th>
                        <th>Petugas Pelaksana</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-end">Waktu Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->stockMovements as $sm)
                        <tr>
                            <td>
                                @if($sm->type === 'IN')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                                        <i class="bi bi-box-arrow-in-down me-1"></i>MASUK (IN)
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">
                                        <i class="bi bi-box-arrow-up me-1"></i>KELUAR (OUT)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $sm->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                {{ $sm->type === 'IN' ? '+' : '-' }}{{ number_format($sm->quantity) }}
                            </td>
                            <td>
                                <span class="fw-semibold"><i class="bi bi-person me-1 text-muted"></i>{{ $sm->user->name ?? 'System' }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $sm->notes ?: '-' }}</span>
                            </td>
                            <td class="text-end text-muted small">
                                <div>{{ $sm->created_at->format('d M Y H:i') }}</div>
                                <span class="text-secondary" style="font-size: 0.75rem;">({{ $sm->created_at->diffForHumans() }})</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada riwayat transaksi barang masuk/keluar untuk produk ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
