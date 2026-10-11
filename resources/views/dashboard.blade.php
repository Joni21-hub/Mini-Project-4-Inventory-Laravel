@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Inventaris')
@section('page_title', 'Dashboard Utama')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Row 1: Summary Metric Cards -->
    <div class="row g-3">
        <!-- Total Produk -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Produk</div>
                        <h4 class="fw-bold mb-0">{{ number_format($totalProducts) }} <span class="fs-6 text-muted fw-normal">Item</span></h4>
                        <small class="text-secondary">{{ $totalCategories }} Kategori aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Inventaris -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Valuasi Aset</div>
                        <h4 class="fw-bold mb-0" style="font-size: 1.35rem;">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ number_format($totalStockQty) }} unit fisik tersimpan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Menipis / Kritis -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon {{ ($lowStockCount + $outOfStockCount) > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Peringatan Stok</div>
                        <h4 class="fw-bold mb-0 text-danger">
                            {{ $lowStockCount + $outOfStockCount }} <span class="fs-6 fw-normal text-muted">Perlu Restock</span>
                        </h4>
                        <small class="text-muted">{{ $outOfStockCount }} Habis &bull; {{ $lowStockCount }} Menipis</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mutasi Bulan Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Aktivitas Bulan Ini</div>
                        <h5 class="fw-bold mb-0 d-flex gap-2 align-items-center">
                            <span class="text-success"><i class="bi bi-arrow-down-short"></i>+{{ number_format($monthlyIn) }}</span>
                            <span class="text-muted">/</span>
                            <span class="text-danger"><i class="bi bi-arrow-up-short"></i>-{{ number_format($monthlyOut) }}</span>
                        </h5>
                        <small class="text-muted">{{ now()->isoFormat('MMMM Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts -->
    <div class="row g-4">
        <!-- 7-Day In/Out Bar Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Tren Mutasi Stok 7 Hari Terakhir</h6>
                        <small class="text-muted">Perbandingan kuantitas barang masuk (IN) vs barang keluar (OUT)</small>
                    </div>
                    <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border">
                        Detail Riwayat <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="movementTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution Donut Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill text-info me-2"></i>Distribusi Kategori</h6>
                    <small class="text-muted">Proporsi produk berdasarkan kategori</small>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="max-width: 250px; width: 100%;">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Critical Low Stock Alerts & Recent Transactions -->
    <div class="row g-4">
        <!-- Low Stock Alerts -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-bell-fill me-2"></i>Peringatan Stok Menipis</h6>
                        <small class="text-muted">Produk yang berada pada atau di bawah batas minimum</small>
                    </div>
                    <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="btn btn-sm btn-outline-danger">
                        Semua Kritis
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Batas Min</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $lowItem)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $lowItem->name }}</div>
                                        <small class="text-muted font-monospace">{{ $lowItem->sku }}</small>
                                    </td>
                                    <td class="text-center fw-bold {{ $lowItem->stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $lowItem->stock }}
                                    </td>
                                    <td class="text-center text-muted">{{ $lowItem->minimum_stock }}</td>
                                    <td class="text-center">
                                        @if($lowItem->stock <= 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-status">Habis</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle badge-status">Menipis</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('stock.in', ['product_id' => $lowItem->id]) }}" class="btn btn-sm btn-success py-1 px-2" title="Restock">
                                            <i class="bi bi-box-arrow-in-down"></i> Masuk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                        Semua stok produk saat ini dalam batas aman!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-secondary me-2"></i>Aktivitas Mutasi Terkini</h6>
                        <small class="text-muted">7 pencatatan pergerakan inventaris terbaru</small>
                    </div>
                    <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border">
                        Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tipe</th>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th>Petugas</th>
                                <th class="text-end">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                                <tr>
                                    <td>
                                        @if($movement->type === 'IN')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                                                <i class="bi bi-arrow-down me-1"></i>IN
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">
                                                <i class="bi bi-arrow-up me-1"></i>OUT
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $movement->product->name ?? 'Produk dihapus' }}</span>
                                    </td>
                                    <td class="text-center fw-bold {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                        {{ $movement->type === 'IN' ? '+' : '-' }}{{ $movement->quantity }}
                                    </td>
                                    <td>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $movement->user->name ?? 'Sistem' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted" title="{{ $movement->created_at->format('d/m/Y H:i') }}">
                                            {{ $movement->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada riwayat mutasi stok tercatat.
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

@push('scripts')
<script>
    // 1. Trend Chart (Bar: IN vs OUT)
    const ctxTrend = document.getElementById('movementTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Barang Masuk (IN)',
                    data: {!! json_encode($chartDataIn) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.75)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Barang Keluar (OUT)',
                    data: {!! json_encode($chartDataOut) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // 2. Category Donut Chart
    const ctxCategory = document.getElementById('categoryDonutChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
                data: {!! json_encode($categoryCounts) !!},
                backgroundColor: [
                    '#4f46e5',
                    '#06b6d4',
                    '#f59e0b',
                    '#10b981',
                    '#ec4899',
                    '#8b5cf6',
                    '#64748b'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>
@endpush
