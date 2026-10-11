<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Inventory Management System</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #e0e7ff;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #ffffff;
            --sidebar-active-bg: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
        }

        .sidebar-brand i {
            font-size: 1.5rem;
            color: #6366f1;
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .sidebar-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 1.25rem;
            text-align: center;
        }

        .sidebar-link:hover {
            color: var(--sidebar-active);
            background-color: var(--sidebar-active-bg);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        /* Content Area */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        /* Cards & Components */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--card-border);
        }

        .badge-status {
            padding: 0.35em 0.7em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="bi bi-boxes"></i>
            <div>
                <span class="d-block lh-1">InvenTech</span>
                <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 400;">Laravel 12 Edition</span>
            </div>
        </a>

        <ul class="sidebar-menu">
            <li class="sidebar-heading">Menu Utama</li>
            <li class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-heading">Inventaris Barang</li>
            <li class="sidebar-item">
                <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Produk</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Kategori</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Supplier</span>
                </a>
            </li>

            <li class="sidebar-heading">Transaksi Stok</li>
            <li class="sidebar-item">
                <a href="{{ route('stock.in') }}" class="sidebar-link {{ request()->routeIs('stock.in') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-down text-success"></i>
                    <span>Barang Masuk (IN)</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('stock.out') }}" class="sidebar-link {{ request()->routeIs('stock.out') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-up text-danger"></i>
                    <span>Barang Keluar (OUT)</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('stock.history') }}" class="sidebar-link {{ request()->routeIs('stock.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Mutasi</span>
                </a>
            </li>

            <li class="sidebar-heading">Analitik & Laporan</li>
            <li class="sidebar-item">
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Laporan & Ekspor</span>
                </a>
            </li>
        </ul>

        <div class="px-3 mt-auto mb-3" style="position: absolute; bottom: 0; width: 100%;">
            <div class="p-3 rounded-3" style="background: #1e293b;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white text-truncate fw-semibold" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                        <span class="badge {{ (Auth::user()->role ?? '') === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase" style="font-size: 0.65rem;">
                            {{ Auth::user()->role ?? 'Staff' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" type="button" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-block">
                    <h6 class="mb-0 fw-bold">@yield('page_title', 'Sistem Manajemen Inventaris')</h6>
                    <small class="text-muted">Universitas / Lab Pemrograman Web - P5 Laravel</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Quick Action Buttons -->
                <a href="{{ route('stock.in') }}" class="btn btn-sm btn-outline-success d-none d-md-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i> Stok Masuk
                </a>
                <a href="{{ route('stock.out') }}" class="btn btn-sm btn-outline-danger d-none d-md-inline-flex align-items-center gap-1">
                    <i class="bi bi-dash-circle"></i> Stok Keluar
                </a>

                <!-- User Dropdown Menu -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5 text-secondary"></i>
                        <span class="fw-medium text-dark d-none d-sm-inline">{{ Auth::user()->name ?? 'Akun' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2 border-bottom">
                            <span class="d-block fw-bold text-dark">{{ Auth::user()->name ?? 'Akun' }}</span>
                            <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                            <div>
                                <span class="badge {{ (Auth::user()->role ?? '') === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase mt-1" style="font-size: 0.65rem;">
                                    Role: {{ Auth::user()->role ?? 'Staff' }}
                                </span>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('profile') }}">
                                <i class="bi bi-person text-primary"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle-fill"></i> Terdapat kesalahan pada input Anda:
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted" style="font-size: 0.85rem;">
            <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <span>&copy; {{ date('Y') }} <strong>InvenTech Laravel 12</strong> — Pertemuan 5 Praktikum Pemrograman Web</span>
                <span>Dibangun dengan Eloquent ORM, Pessimistic Locking & Bootstrap 5</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
