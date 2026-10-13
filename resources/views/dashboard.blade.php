@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('page_title', 'Ringkasan Eksekutif & Analitik')

@section('content')
<div class="d-flex flex-column gap-4">

    <!-- Row 1: Primary Metric Cards (Rich Gradients & Layered Depth) -->
    <div class="row g-3">
        <!-- Metric 1: Total Produk -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-hover-lift h-100 border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: 1px solid #e0e7ff !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Produk</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem; font-weight: 700;">{{ $totalCategories }} Kategori</span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.1rem; letter-spacing: -0.03em;">{{ number_format($totalProducts) }}</h2>
                        <span class="text-muted fw-semibold" style="font-size: 0.88rem;">Item Master</span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.78rem;">Katalog aktif terindeks di sistem</p>
                </div>
            </div>
        </div>

        <!-- Metric 2: Total Valuasi Persediaan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-hover-lift h-100 border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 1px solid #bbf7d0 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Total Valuasi Aset</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem; font-weight: 700;">Fisik Riil</span>
                    </div>
                    <div class="mb-1">
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.55rem; letter-spacing: -0.02em;">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</h3>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.78rem;">Akumulasi dari {{ number_format($totalStockQty) }} unit tersimpan</p>
                </div>
            </div>
        </div>

        <!-- Metric 3: Peringatan Stok Restock -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-hover-lift h-100 border-0" style="background: linear-gradient(135deg, #ffffff 0%, {{ ($lowStockCount + $outOfStockCount) > 0 ? '#fff1f2' : '#f0fdf4' }} 100%); border: 1px solid {{ ($lowStockCount + $outOfStockCount) > 0 ? '#fecdd3' : '#bbf7d0' }} !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Peringatan Stok</span>
                        @if(($lowStockCount + $outOfStockCount) > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem; font-weight: 700;">Perlu Restock</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem; font-weight: 700;">Semua Aman</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-bold mb-0 {{ ($lowStockCount + $outOfStockCount) > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 2.1rem; letter-spacing: -0.03em;">
                            {{ $lowStockCount + $outOfStockCount }}
                        </h2>
                        <span class="text-muted fw-semibold" style="font-size: 0.88rem;">Item Kritis</span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.78rem;">{{ $outOfStockCount }} stok habis &bull; {{ $lowStockCount }} menipis</p>
                </div>
            </div>
        </div>

        <!-- Metric 4: Aktivitas Mutasi Bulanan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-hover-lift h-100 border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border: 1px solid #bae6fd !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.08em;">Aktivitas Mutasi</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.68rem; font-weight: 700;">{{ now()->isoFormat('MMMM Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.88rem;">
                            +{{ number_format($monthlyIn) }} Masuk
                        </span>
                        <span class="text-muted">/</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.88rem;">
                            -{{ number_format($monthlyOut) }} Keluar
                        </span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.78rem;">Siklus barang keluar & masuk bulan ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Analytics Charts (7-Day Trend & Category Composition) -->
    <div class="row g-4">
        <!-- 7-Day Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tren Mutasi Stok 7 Hari Terakhir</h6>
                        <small class="text-muted">Perbandingan kuantitas barang masuk (IN) vs barang keluar (OUT)</small>
                    </div>
                    <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border py-1 px-3">
                        Lihat Riwayat
                    </a>
                </div>
                <div class="card-body pt-2 pb-4">
                    <canvas id="movementTrendChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Composition Donut -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h6 class="fw-bold mb-0 text-dark">Distribusi Kategori Produk</h6>
                    <small class="text-muted">Proporsi produk berdasarkan klasifikasi</small>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                    <div style="max-width: 240px; width: 100%;">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Visual Tables (Low Stock Alerts & Recent Transactions) -->
    <div class="row g-4">
        <!-- Table 1: Peringatan Stok Menipis -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-danger">Peringatan Level Stok Menipis</h6>
                        <small class="text-muted">Daftar produk dengan stok di bawah batas minimum</small>
                    </div>
                    <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="btn btn-sm btn-outline-danger py-1 px-3">
                        Filter Kritis
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Sisa</th>
                                <th class="text-center">Min</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $lowItem)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-thumb-container">
                                                <img src="{{ $lowItem->image_url }}" alt="{{ $lowItem->name }}" loading="lazy">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">{{ $lowItem->name }}</div>
                                                <span class="badge-sku">{{ $lowItem->sku }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold {{ $lowItem->stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $lowItem->stock }}
                                    </td>
                                    <td class="text-center text-muted fw-medium">{{ $lowItem->minimum_stock }}</td>
                                    <td class="text-center">
                                        @if($lowItem->stock <= 0)
                                            <span class="status-badge badge-out">
                                                <span class="dot"></span> Habis
                                            </span>
                                        @else
                                            <span class="status-badge badge-low">
                                                <span class="dot"></span> Menipis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('stock.in', ['product_id' => $lowItem->id]) }}" class="btn btn-sm btn-success py-1 px-3" style="font-size: 0.78rem;">
                                            + Restock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-1 p-2">Semua Stok Berada Pada Batas Aman</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Table 2: Aktivitas Mutasi Terkini -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Aktivitas Mutasi Terkini</h6>
                        <small class="text-muted">7 pencatatan pergerakan inventaris terbaru</small>
                    </div>
                    <a href="{{ route('stock.history') }}" class="btn btn-sm btn-light border py-1 px-3">
                        Semua Riwayat
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
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
                                            <span class="badge-type-in">+ IN</span>
                                        @else
                                            <span class="badge-type-out">- OUT</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($movement->product)
                                                <div class="product-thumb-container" style="width: 36px; height: 36px;">
                                                    <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" loading="lazy">
                                                </div>
                                                <div class="text-truncate" style="max-width: 170px;">
                                                    <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.84rem;">{{ $movement->product->name }}</span>
                                                    <span class="text-muted" style="font-size: 0.72rem;">{{ $movement->product->sku }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">Produk dihapus</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                        {{ $movement->type === 'IN' ? '+' : '-' }}{{ $movement->quantity }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.75rem;">
                                            {{ $movement->user->name ?? 'Sistem' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted" style="font-size: 0.75rem;">
                                        {{ $movement->created_at->diffForHumans() }}
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
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: 'Barang Keluar (OUT)',
                    data: {!! json_encode($chartDataOut) !!},
                    backgroundColor: 'rgba(244, 63, 94, 0.85)',
                    borderColor: 'rgb(244, 63, 94)',
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 14,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: 600,
                            size: 12
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(226, 232, 240, 0.6)'
                    }
                },
                x: {
                    grid: {
                        display: false
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
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 12,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                            weight: 500
                        }
                    }
                }
            },
            cutout: '72%'
        }
    });
</script>
@endpush
