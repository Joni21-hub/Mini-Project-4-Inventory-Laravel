@extends('layouts.app')

@section('title', 'Riwayat Mutasi Stok')
@section('page_title', 'Audit Trail Pergerakan Stok')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Header & Action Toolbar -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Riwayat Lengkap Mutasi Stok (Audit Trail)</h5>
                    <p class="text-muted small mb-0">Catatan log legal pergerakan stok masuk dan keluar dengan pencatatan penanggung jawab operasional.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('reports.export.movements', request()->query()) }}" class="btn btn-outline-success py-1 px-3">
                        Ekspor CSV
                    </a>
                    <a href="{{ route('stock.in') }}" class="btn btn-success py-1 px-3">
                        + Stok Masuk
                    </a>
                    <a href="{{ route('stock.out') }}" class="btn btn-danger py-1 px-3">
                        - Stok Keluar
                    </a>
                </div>
            </div>

            <hr class="my-3" style="border-color: #f1f5f9;">

            <!-- Filter Toolbar -->
            <form action="{{ route('stock.history') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>Barang Masuk (IN)</option>
                        <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>Barang Keluar (OUT)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                [{{ $prod->sku }}] {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" title="Dari Tanggal" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" title="Sampai Tanggal" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        Saring
                    </button>
                    @if(request()->hasAny(['type', 'product_id', 'date_from', 'date_to']))
                        <a href="{{ route('stock.history') }}" class="btn btn-light border px-3" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Movements Data Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">No / ID</th>
                        <th style="width: 130px;">Tipe Mutasi</th>
                        <th>Produk Terkait</th>
                        <th class="text-center" style="width: 120px;">Kuantitas</th>
                        <th>Petugas Pelaksana</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-end" style="width: 170px;">Waktu Catat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>
                                <span class="badge-sku">#{{ $movement->id }}</span>
                            </td>
                            <td>
                                @if($movement->type === 'IN')
                                    <span class="badge-type-in">+ MASUK (IN)</span>
                                @else
                                    <span class="badge-type-out">- KELUAR (OUT)</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($movement->product)
                                        <div class="product-thumb-container" style="width: 42px; height: 42px;">
                                            <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" loading="lazy">
                                        </div>
                                        <div>
                                            <a href="{{ route('products.show', $movement->product) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.88rem;">
                                                {{ $movement->product->name }}
                                            </a>
                                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                                {{ $movement->product->sku }} &bull; {{ $movement->product->category->name ?? '-' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">[Produk telah dihapus]</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                {{ $movement->type === 'IN' ? '+' : '-' }}{{ number_format($movement->quantity) }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.84rem;">{{ $movement->user->name ?? 'Sistem' }}</div>
                                <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.65rem;">{{ $movement->user->role ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $movement->notes ?: '-' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="small fw-semibold text-dark">{{ $movement->created_at->format('d M Y') }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $movement->created_at->format('H:i:s') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <span class="d-block fw-bold text-dark mb-1">Belum ada catatan mutasi yang cocok dengan kriteria saring.</span>
                                <span class="d-block small">Silakan pilih tanggal lain atau bersihkan parameter filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} dari {{ $movements->total() }} entri mutasi
                </small>
                <div>
                    {{ $movements->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
