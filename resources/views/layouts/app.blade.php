<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — InvenTech Enterprise</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', 'Plus Jakarta Sans', sans-serif;
            
            /* Color Palette: Tech Luxury Modern */
            --canvas: #f6f8fc;
            --surface: #ffffff;
            --surface-elevated: #ffffff;
            
            --sidebar-bg: #090d16;
            --sidebar-border: rgba(255, 255, 255, 0.07);
            --sidebar-text: #94a3b8;
            --sidebar-hover: #f1f5f9;
            
            --brand-primary: #4f46e5;
            --brand-indigo: #6366f1;
            --brand-cyan: #06b6d4;
            --brand-emerald: #10b981;
            --brand-rose: #f43f5e;
            --brand-amber: #f59e0b;
            
            --text-strong: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-subtle: #94a3b8;
            
            --border-subtle: #e2e8f0;
            --border-focus: #818cf8;
            
            /* Gradients */
            --grad-primary: linear-gradient(135deg, #4338ca 0%, #6366f1 55%, #4f46e5 100%);
            --grad-success: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --grad-danger: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);
            --grad-warning: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            --grad-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --grad-mesh: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%), radial-gradient(at 100% 100%, rgba(6, 182, 212, 0.04) 0px, transparent 50%);
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-card: 0 1px 3px rgba(15, 23, 42, 0.03), 0 8px 24px -8px rgba(15, 23, 42, 0.05);
            --shadow-hover: 0 16px 36px -12px rgba(15, 23, 42, 0.1), 0 2px 6px rgba(15, 23, 42, 0.03);
            --glow-primary: 0 6px 20px rgba(99, 102, 241, 0.35);
            --glow-success: 0 6px 20px rgba(16, 185, 129, 0.3);
            --glow-danger: 0 6px 20px rgba(244, 63, 94, 0.3);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--canvas);
            background-image: var(--grad-mesh);
            color: var(--text-body);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.011em;
        }

        h1, h2, h3, h4, h5, h6, .brand-title {
            font-family: var(--font-heading);
            letter-spacing: -0.025em;
        }

        /* Ambient Global Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ─── SIDEBAR ARCHITECTURE ─── */
        .sidebar {
            width: 270px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sidebar-brand {
            padding: 1.75rem 1.5rem 1.25rem;
            display: block;
            text-decoration: none;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand .brand-logo-text {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 30%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.03em;
            display: block;
            line-height: 1.1;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
            margin-top: 0.25rem;
            display: block;
        }

        .sidebar-nav-container {
            flex-grow: 1;
            padding: 1.25rem 0.85rem;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            padding: 1rem 0.85rem 0.4rem;
        }

        .sidebar-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav-item {
            margin-bottom: 0.3rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.95rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.865rem;
            font-weight: 500;
            border-radius: 0.65rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .sidebar-nav-link .link-label-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-bullet {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: #475569;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .nav-bullet.bullet-success {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        }

        .nav-bullet.bullet-danger {
            background: #f43f5e;
            box-shadow: 0 0 8px rgba(244, 63, 94, 0.6);
        }

        .sidebar-nav-link:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(2px);
        }

        .sidebar-nav-link:hover .nav-bullet {
            background: #818cf8;
            transform: scale(1.3);
        }

        .sidebar-nav-link.active {
            color: #ffffff;
            font-weight: 600;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.22) 0%, rgba(124, 58, 237, 0.16) 100%);
            border: 1px solid rgba(129, 140, 248, 0.32);
            box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.28);
        }

        .sidebar-nav-link.active .nav-bullet {
            background: #818cf8;
            box-shadow: 0 0 10px #818cf8;
            transform: scale(1.4);
        }

        .nav-pill-tag {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem 1.5rem;
            border-top: 1px solid var(--sidebar-border);
            background: rgba(0, 0, 0, 0.2);
        }

        .user-profile-strip {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.65rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 0.55rem;
            background: var(--grad-primary);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: var(--glow-primary);
        }

        /* ─── MAIN CONTENT LAYOUT ─── */
        .main-content {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Header Navbar */
        .topbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.95rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-badge {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--brand-indigo);
            display: inline-block;
            margin-bottom: 0.15rem;
        }

        .page-heading-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-strong);
            margin: 0;
            line-height: 1.2;
        }

        /* ─── BUTTONS SYSTEM (PREMIUM GRADIENTS & MICRO-INTERACTIONS) ─── */
        .btn {
            font-family: var(--font-main);
            font-weight: 600;
            border-radius: 0.65rem;
            padding: 0.55rem 1.15rem;
            font-size: 0.875rem;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn:active {
            transform: scale(0.97) !important;
        }

        .btn-primary {
            background: var(--grad-primary);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: var(--shadow-sm), var(--glow-primary);
        }
        .btn-primary:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 50%, #4338ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.45);
        }

        .btn-success {
            background: var(--grad-success);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: var(--shadow-sm), var(--glow-success);
        }
        .btn-success:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: var(--grad-danger);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: var(--shadow-sm), var(--glow-danger);
        }
        .btn-danger:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #be123c 0%, #e11d48 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(244, 63, 94, 0.4);
        }

        .btn-outline-primary {
            background: #ffffff;
            color: var(--brand-primary);
            border: 1.5px solid #c7d2fe;
            box-shadow: var(--shadow-sm);
        }
        .btn-outline-primary:hover {
            background: #eef2ff;
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            background: #ffffff;
            color: var(--brand-rose);
            border: 1.5px solid #fecdd3;
            box-shadow: var(--shadow-sm);
        }
        .btn-outline-danger:hover {
            background: #fff1f2;
            color: #be123c;
            border-color: var(--brand-rose);
            transform: translateY(-1px);
        }

        .btn-light, .btn-secondary-soft {
            background: #ffffff;
            color: #334155;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-sm);
        }
        .btn-light:hover, .btn-secondary-soft:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }

        /* Pill Action Buttons */
        .btn-pill-in {
            background: var(--grad-success);
            color: #ffffff;
            padding: 0.42rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }
        .btn-pill-in:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
        }

        .btn-pill-out {
            background: var(--grad-danger);
            color: #ffffff;
            padding: 0.42rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(244, 63, 94, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }
        .btn-pill-out:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(244, 63, 94, 0.45);
        }

        /* ─── CARDS & SURFACES ─── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border-subtle);
            border-radius: 1.15rem;
            box-shadow: var(--shadow-card);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-subtle);
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            color: var(--text-strong);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-hover-lift {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }
        .card-hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: #cbd5e1;
        }

        /* ─── STATUS PILLS & BADGES (CLEAN TYPOGRAPHY, NO ICONS) ─── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.32rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .status-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .badge-safe {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-safe .dot {
            background: #10b981;
            box-shadow: 0 0 6px #10b981;
        }

        .badge-low {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-low .dot {
            background: #f59e0b;
            box-shadow: 0 0 6px #f59e0b;
        }

        .badge-out {
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }
        .badge-out .dot {
            background: #f43f5e;
            box-shadow: 0 0 6px #f43f5e;
        }

        .badge-type-in {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 0.45rem;
            font-size: 0.75rem;
        }

        .badge-type-out {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 0.45rem;
            font-size: 0.75rem;
        }

        .badge-sku {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-family: monospace;
            font-weight: 700;
            padding: 0.22rem 0.55rem;
            border-radius: 0.45rem;
            font-size: 0.75rem;
        }

        /* ─── TABLES DESIGN ─── */
        .table {
            color: var(--text-body);
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table > thead > tr > th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            background: #f8fafc;
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.95rem 1.15rem;
        }

        .table > tbody > tr > td {
            padding: 1rem 1.15rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }

        .table-hover > tbody > tr:hover > td {
            background-color: #f8fafc;
        }

        /* ─── FORMS & INPUTS ─── */
        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.35rem;
            letter-spacing: 0.01em;
        }

        .form-control, .form-select {
            font-family: var(--font-main);
            border: 1.5px solid var(--border-subtle);
            border-radius: 0.65rem;
            padding: 0.65rem 0.95rem;
            font-size: 0.9rem;
            color: var(--text-strong);
            background-color: #ffffff;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }

        /* ─── PRODUCT THUMBNAILS (E-COMMERCE LEVEL REAL PHOTOGRAPHY) ─── */
        .product-thumb-container {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            overflow: hidden;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
            position: relative;
        }

        .product-thumb-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        tr:hover .product-thumb-container img {
            transform: scale(1.1);
        }

        /* Mobile Drawer */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ─── SIDEBAR NAVIGATION (CALM, CLEAN, NO ICON GLYPHS) ─── -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand Header -->
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <span class="brand-logo-text">INVENTECH</span>
            <span class="brand-sub">Inventory Intelligence</span>
        </a>

        <!-- Navigation Menu -->
        <div class="sidebar-nav-container">
            <div class="nav-section-title">Menu Utama</div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Dashboard Analitik</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="nav-section-title">Katalog & Master</div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="{{ route('products.index') }}" class="sidebar-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Katalog Produk</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('categories.index') }}" class="sidebar-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Kategori Produk</span>
                        </div>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('suppliers.index') }}" class="sidebar-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Mitra Supplier</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="nav-section-title">Transaksi Stok</div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="{{ route('stock.in') }}" class="sidebar-nav-link {{ request()->routeIs('stock.in') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet bullet-success"></span>
                            <span>Barang Masuk (IN)</span>
                        </div>
                        <span class="nav-pill-tag bg-success-subtle text-success">+Masuk</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('stock.out') }}" class="sidebar-nav-link {{ request()->routeIs('stock.out') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet bullet-danger"></span>
                            <span>Barang Keluar (OUT)</span>
                        </div>
                        <span class="nav-pill-tag bg-danger-subtle text-danger">-Keluar</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('stock.history') }}" class="sidebar-nav-link {{ request()->routeIs('stock.history') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Riwayat Mutasi</span>
                        </div>
                    </a>
                </li>
            </ul>

            <div class="nav-section-title">Laporan & Audit</div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="{{ route('reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="link-label-group">
                            <span class="nav-bullet"></span>
                            <span>Rekapitulasi & CSV</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer User Profile -->
        <div class="sidebar-footer">
            <div class="user-profile-strip">
                <div class="avatar-initials">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="text-white text-truncate fw-semibold" style="font-size: 0.82rem;">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                    <span class="badge {{ (Auth::user()->role ?? '') === 'admin' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} text-uppercase" style="font-size: 0.62rem; font-weight: 700;">
                        {{ Auth::user()->role ?? 'Staff' }}
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <!-- ─── MAIN CONTENT WRAPPER ─── -->
    <div class="main-content">
        <!-- Topbar Header -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none py-1 px-2 border" type="button" id="sidebarToggle">
                    <span style="font-size: 0.85rem; font-weight: 700;">MENU</span>
                </button>
                <div>
                    <span class="page-badge">@yield('page_title', 'Sistem Manajemen Inventaris')</span>
                    <h5 class="page-heading-title">@yield('title', 'Dashboard')</h5>
                </div>
            </div>

            <!-- Quick Action Pills & User Dropdown -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('stock.in') }}" class="btn-pill-in d-none d-sm-inline-flex">
                    <span>+ Catat Masuk</span>
                </a>
                <a href="{{ route('stock.out') }}" class="btn-pill-out d-none d-sm-inline-flex">
                    <span>- Catat Keluar</span>
                </a>

                <!-- Account Dropdown Menu -->
                <div class="dropdown ms-2">
                    <button class="btn btn-light btn-sm dropdown-toggle py-1 px-3 border rounded-pill d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fw-semibold text-dark" style="font-size: 0.82rem;">{{ Auth::user()->name ?? 'Akun' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Akun' }}</span>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ Auth::user()->email ?? '' }}</small>
                            <span class="badge {{ (Auth::user()->role ?? '') === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} text-uppercase mt-1" style="font-size: 0.65rem;">
                                Role: {{ Auth::user()->role ?? 'Staff' }}
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 rounded-2 fw-medium" href="{{ route('profile') }}" style="font-size: 0.82rem;">
                                Pengaturan Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 rounded-2 text-danger fw-semibold" style="font-size: 0.82rem;">
                                    Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Stage -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex justify-content-between align-items-center rounded-3 p-3 mb-4" role="alert" style="background: #ecfdf5; border-left: 4px solid #10b981 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success text-white px-2 py-1 rounded-2 fw-bold" style="font-size: 0.72rem;">SUKSES</span>
                        <div class="text-dark fw-medium" style="font-size: 0.88rem;">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex justify-content-between align-items-center rounded-3 p-3 mb-4" role="alert" style="background: #fff1f2; border-left: 4px solid #f43f5e !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white px-2 py-1 rounded-2 fw-bold" style="font-size: 0.72rem;">PERINGATAN</span>
                        <div class="text-dark fw-medium" style="font-size: 0.88rem;">{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 p-3 mb-4" role="alert" style="background: #fff1f2; border-left: 4px solid #f43f5e !important;">
                    <div class="fw-bold mb-2 text-danger" style="font-size: 0.88rem;">
                        Terdapat kesalahan pada input Anda:
                    </div>
                    <ul class="mb-0 ps-3 text-dark" style="font-size: 0.84rem;">
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
        <footer class="bg-white border-top py-3 px-4 text-muted" style="font-size: 0.82rem;">
            <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <span>&copy; {{ date('Y') }} <strong>InvenTech Enterprise</strong> — Sistem Manajemen Inventaris & Mutasi Stok</span>
                <span class="text-secondary">Arsitektur Laravel 12 &bull; Eloquent ORM &bull; Pessimistic Concurrency</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JavaScript Bundle -->
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
