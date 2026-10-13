@extends('layouts.app')

@section('title', 'Laporan & Analitik Inventaris')
@section('page_title', 'Audit & Pusat Rekapitulasi')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Top Action Card: Filter & CSV Downloads -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Pusat Laporan & Rekapitulasi Audit</h5>
                    <p class="text-muted small mb-0">Analisis menyeluruh status inventaris, valuasi aset per kategori, dan ekspor riwayat pergudangan.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('reports.export.products') }}" class="btn btn-outline-primary py-2 px-3">
                        Ekspor CSV Produk
                    </a>
                    <a href="{{ route('reports.export.movements', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success py-2 px-3 shadow-sm">
                        Ekspor CSV Mutasi Stok
                    </a>
                </div>
            </div>

            <hr class="my-3" style="border-color: #f1f5f9;">

            <!-- Date Range Filter Form -->
            <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Periode Tanggal Mulai</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Periode Tanggal Selesai</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-sm-4 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Terapkan Periode
                    </button>
                </div>
                @if($dateFrom || $dateTo)
                    <div class="col-auto d-flex align-items-end">
                        <a href="{{ route('reports.index') }}" class="btn btn-light border px-3" title="Reset">
                            Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Metrics for the Selected Period -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: 1px solid #e0e7ff !important;">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Log Transaksi</span>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 2rem;">{{ number_format($totalMovements) }}</h2>
                    <span class="text-muted fw-semibold">Kali Pencatatan</span>
                </div>
                <small class="text-secondary">{{ $dateFrom || $dateTo ? 'Dalam rentang periode terpilih' : 'Sepanjang riwayat operasional' }}</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 1px solid #bbf7d0 !important;">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Kuantitas Masuk</span>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-success" style="font-size: 2rem;">+{{ number_format($totalInQty) }}</h2>
                    <span class="text-muted fw-semibold">Unit Fisik</span>
                </div>
                <small class="text-secondary">Penerimaan pasokan gudang</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%); border: 1px solid #fecdd3 !important;">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Kuantitas Keluar</span>
                <div class="d-flex align-items-baseline gap-2 my-1">
                    <h2 class="fw-bold mb-0 text-danger" style="font-size: 2rem;">-{{ number_format($totalOutQty) }}</h2>
                    <span class="text-muted fw-semibold">Unit Fisik</span>
                </div>
                <small class="text-secondary">Pengiriman & alokasi operasional</small>
            </div>
        </div>
    </div>

    <!-- Section: Category Valuation Breakdown -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-transparent py-3">
            <h6 class="fw-bold mb-0 text-dark">Rekapitulasi Valuasi & Persediaan per Kategori</h6>
            <small class="text-muted">Distribusi kuantitas fisik dan nilai rupiah berdasarkan klasifikasi</small>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
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
                                <span class="fw-bold text-dark fs-6">{{ $cat->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border px-2 py-1">{{ $cat->products_count }} Produk</span>
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

    <!-- Section: 5 Most Active Products & Critical Stock Audit with Real Product Thumbnails -->
    <div class="row g-4">
        <!-- Most Active Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-dark">Top 5 Produk Paling Sering Dimutasi</h6>
                    <small class="text-muted">Item dengan frekuensi sirkulasi keluar/masuk paling dinamis</small>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="product-thumb-container" style="width: 36px; height: 36px;">
                                                <img src="{{ $top->image_url }}" alt="{{ $top->name }}" loading="lazy">
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $top) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.85rem;">
                                                    {{ $top->name }}
                                                </a>
                                                <span class="badge-sku" style="font-size: 0.68rem;">{{ $top->sku }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">{{ $top->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                            {{ $top->stock_movements_count }} transaksi
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        {{ number_format($top->stock) }} unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Critical Stock Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-danger">Daftar Audit Restock Kritis</h6>
                    <small class="text-muted">Semua produk yang memerlukan pesanan pengadaan segera</small>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="product-thumb-container" style="width: 36px; height: 36px;">
                                                <img src="{{ $crit->image_url }}" alt="{{ $crit->name }}" loading="lazy">
                                            </div>
                                            <div>
                                                <a href="{{ route('products.show', $crit) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.85rem;">
                                                    {{ $crit->name }}
                                                </a>
                                                <span class="badge-sku" style="font-size: 0.68rem;">{{ $crit->sku }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold {{ $crit->stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $crit->stock }}
                                    </td>
                                    <td class="text-center text-muted fw-medium">{{ $crit->minimum_stock }}</td>
                                    <td class="text-end px-3">
                                        <a href="{{ route('stock.in', ['product_id' => $crit->id]) }}" class="btn btn-sm btn-success py-1 px-3" style="font-size: 0.78rem;">
                                            + Restock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle p-2">Semua Stok Berada Pada Batas Aman</span>
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
