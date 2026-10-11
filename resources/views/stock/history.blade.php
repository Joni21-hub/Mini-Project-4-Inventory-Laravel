@extends('layouts.app')

@section('title', 'Riwayat Mutasi Stok')
@section('page_title', 'Histori Pergerakan Inventaris')

@section('content')
<div class="d-flex flex-column gap-3">

    <!-- Header Actions & Filters -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Lengkap Mutasi Stok (Audit Trail)</h5>
                    <p class="text-muted small mb-0">Semua catatan log pergerakan stok masuk dan keluar dengan pencatatan pengguna yang bertanggung jawab.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.export.movements', request()->query()) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
                    </a>
                    <a href="{{ route('stock.in') }}" class="btn btn-success d-inline-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                    </a>
                    <a href="{{ route('stock.out') }}" class="btn btn-danger d-inline-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up"></i> Stok Keluar
                    </a>
                </div>
            </div>

            <hr class="my-3">

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
                           value="{{ request('date_from') }}" placeholder="Dari Tanggal">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" title="Sampai Tanggal" 
                           value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Saring
                    </button>
                    @if(request()->hasAny(['type', 'product_id', 'date_from', 'date_to']))
                        <a href="{{ route('stock.history') }}" class="btn btn-light border" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- History Movements Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">No / ID</th>
                        <th style="width: 140px;">Tipe Mutasi</th>
                        <th>Produk</th>
                        <th class="text-center" style="width: 120px;">Kuantitas</th>
                        <th>Petugas Pelaksana</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-end" style="width: 170px;">Waktu Eksekusi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>
                                <span class="text-muted font-monospace small">#{{ $movement->id }}</span>
                            </td>
                            <td>
                                @if($movement->type === 'IN')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1">
                                        <i class="bi bi-box-arrow-in-down me-1"></i>MASUK (IN)
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2 py-1">
                                        <i class="bi bi-box-arrow-up me-1"></i>KELUAR (OUT)
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($movement->product)
                                    <a href="{{ route('products.show', $movement->product) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $movement->product->name }}
                                    </a>
                                    <div class="small text-muted font-monospace">
                                        {{ $movement->product->sku }} &bull; {{ $movement->product->category->name ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">[Produk telah dihapus]</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold fs-6 {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                {{ $movement->type === 'IN' ? '+' : '-' }}{{ number_format($movement->quantity) }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-person me-1 text-muted"></i>{{ $movement->user->name ?? 'Sistem' }}</div>
                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $movement->user->role ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $movement->notes ?: '-' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="small fw-semibold text-dark">{{ $movement->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $movement->created_at->format('H:i:s') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada data mutasi yang cocok dengan filter yang dipilih.
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
