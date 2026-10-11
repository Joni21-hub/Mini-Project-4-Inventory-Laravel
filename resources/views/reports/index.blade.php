@extends('layouts.app')

@section('title', 'Laporan & Analitik Inventaris')
@section('page_title', 'Laporan & Ekspor Data')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Action Card: Filter & CSV Downloads -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Pusat Laporan & Rekapitulasi</h5>
                    <p class="text-muted small mb-0">Analisis menyeluruh status inventaris, valuasi aset, dan pencatatan riwayat pergudangan.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('reports.export.products') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-download"></i> Ekspor CSV Inventaris Produk
                    </a>
                    <a href="{{ route('reports.export.movements', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success d-inline-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV Mutasi Stok
                    </a>
                </div>
            </div>

            <hr class="my-3">

            <!-- Date Range Filter Form -->
            <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Periode Tanggal Mulai</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Periode Tanggal Selesai</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-sm-4 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-filter"></i> Terapkan Periode
                    </button>
                </div>
                @if($dateFrom || $dateTo)
                    <div class="col-auto d-flex align-items-end">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border" title="Reset Rentang Tanggal">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Metrics for the Selected Period -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold">Total Log Mutasi</small>
                        <h4 class="fw-bold mb-0">{{ number_format($totalMovements) }} <span class="fs-6 text-muted fw-normal">Kali</span></h4>
                        <small class="text-secondary">{{ $dateFrom || $dateTo ? 'Periode terpilih' : 'Sepanjang waktu' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold">Total Masuk (IN)</small>
                        <h4 class="fw-bold mb-0 text-success">+{{ number_format($totalInQty) }} <span class="fs-6 text-muted fw-normal">Unit</span></h4>
                        <small class="text-secondary">Barang masuk gudang</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold">Total Keluar (OUT)</small>
                        <h4 class="fw-bold mb-0 text-danger">-{{ number_format($totalOutQty) }} <span class="fs-6 text-muted fw-normal">Unit</span></h4>
                        <small class="text-secondary">Barang keluar gudang</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Category Valuation Breakdown -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-tags-fill text-primary me-2"></i>Rekapitulasi Valuasi & Persediaan per Kategori</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kategori</th>
                        <th class="text-center">Jumlah Jenis Produk</th>
                        <th class="text-center">Total Fisik Unit</th>
                        <th class="text-end">Total Nilai Valuasi (IDR)</th>
                        <th class="text-end px-3">Kontribusi Valuasi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandValuation = $categoryBreakdown->sum('total_valuation');
                    @endphp
                    @forelse($categoryBreakdown as $cat)
                        @php
                            $percentage = $grandValuation > 0 ? round(($cat->total_valuation / $grandValuation) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $cat->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $cat->products_count }} Produk</span>
                            </td>
                            <td class="text-center fw-semibold text-secondary">
                                {{ number_format($cat->products_sum_stock ?? 0) }} unit
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($cat->total_valuation, 0, ',', '.') }}
                            </td>
                            <td class="text-end px-3">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress" style="width: 80px; height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%;"></div>
                                    </div>
                                    <span class="small fw-semibold text-muted" style="width: 45px;">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada kategori terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ $categoryBreakdown->sum('products_count') }} Produk</td>
                        <td class="text-center">{{ number_format($categoryBreakdown->sum('products_sum_stock')) }} unit</td>
                        <td class="text-end text-primary fs-6">Rp {{ number_format($grandValuation, 0, ',', '.') }}</td>
                        <td class="text-end px-3">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Section: 5 Most Active Products & Critical Stock Audit -->
    <div class="row g-4">
        <!-- Most Active Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-fire text-warning me-2"></i>Top 5 Produk Paling Sering Dimutasi</h6>
                    <small class="text-muted">Produk dengan aktivitas transaksi keluar/masuk paling tinggi</small>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Frekuensi</th>
                                <th class="text-end">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topActiveProducts as $top)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $top->name }}</div>
                                        <small class="text-muted font-monospace">{{ $top->sku }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $top->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary px-2 py-1">{{ $top->stock_movements_count }} transaksi</span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($top->stock) }} unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Critical Stock Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-shield-exclamation me-2"></i>Daftar Audit Restock Kritis</h6>
                    <small class="text-muted">Semua produk yang membutuhkan pengadaan segera</small>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Batas Min</th>
                                <th class="text-end px-3">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criticalStockProducts as $crit)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $crit->name }}</div>
                                        <small class="text-muted font-monospace">{{ $crit->sku }}</small>
                                    </td>
                                    <td class="text-center fw-bold {{ $crit->stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $crit->stock }}
                                    </td>
                                    <td class="text-center text-muted">{{ $crit->minimum_stock }}</td>
                                    <td class="text-end px-3">
                                        <a href="{{ route('stock.in', ['product_id' => $crit->id]) }}" class="btn btn-sm btn-success py-1 px-2">
                                            <i class="bi bi-box-arrow-in-down"></i> Restock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                                        Luar biasa! Tidak ada produk dalam status kritis saat ini.
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
