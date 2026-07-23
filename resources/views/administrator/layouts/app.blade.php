<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administrator') — SMAN 1 Garut</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
            --sidebar-bg: #0f1117;
            --sidebar-border: #1e2130;
            --sidebar-text: #8892b0;
            --sidebar-text-active: #ccd6f6;
            --sidebar-hover-bg: rgba(100, 108, 255, 0.08);
            --sidebar-active-bg: rgba(100, 108, 255, 0.18);
            --sidebar-active-border: #646cff;
            --accent: #646cff;
            --accent-glow: rgba(100, 108, 255, 0.3);
            --topbar-height: 64px;
            --bg-main: #0d0f1a;
            --bg-card: #131624;
            --bg-card-hover: #161929;
            --border-color: #1e2236;
            --text-primary: #e2e8f0;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-primary);
            margin: 0;
            overflow-x: hidden;
        }

        /* ===================== SIDEBAR ===================== */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1040;
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        /* Logo area */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
            min-height: var(--topbar-height);
            overflow: hidden;
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 0 16px var(--accent-glow);
        }

        .sidebar-brand-text {
            opacity: 1;
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        .sidebar-brand-text .brand-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: block;
            line-height: 1.2;
        }

        .sidebar-brand-text .brand-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 400;
        }

        #sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; overflow: hidden; }

        /* Nav section label */
        .sidebar-section-label {
            padding: 16px 18px 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            opacity: 1;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .sidebar-section-label { opacity: 0; }

        /* Nav items */
        .sidebar-nav {
            padding: 8px 10px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--sidebar-border); border-radius: 4px; }

        .nav-item-wrapper { margin-bottom: 2px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-text-active);
        }

        .sidebar-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-border);
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--sidebar-active-border);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px var(--accent-glow);
        }

        .sidebar-icon {
            font-size: 17px;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        .sidebar-label {
            flex: 1;
            transition: opacity 0.2s, width 0.2s;
        }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; overflow: hidden; }
        #sidebar.collapsed .sidebar-link .badge { display: none; }

        /* Submenu */
        .submenu-btn {
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .submenu-arrow {
            font-size: 11px;
            margin-left: auto;
            transition: transform 0.2s;
            flex-shrink: 0;
        }
        .submenu-btn[aria-expanded="true"] .submenu-arrow { transform: rotate(180deg); }
        #sidebar.collapsed .submenu-arrow { display: none; }

        .sidebar-submenu {
            padding-left: 32px;
            margin-top: 2px;
        }

        .sidebar-submenu .sidebar-link {
            font-size: 13px;
            padding: 8px 12px;
        }

        /* Sidebar bottom */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--sidebar-border);
        }

        /* Toggle button */
        .sidebar-toggle {
            position: fixed;
            top: 18px;
            left: calc(var(--sidebar-width) - 14px);
            width: 28px;
            height: 28px;
            background: var(--bg-card);
            border: 1px solid var(--sidebar-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1050;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s;
            color: var(--text-muted);
        }
        .sidebar-toggle:hover { color: var(--text-primary); border-color: var(--accent); }
        #sidebar.collapsed ~ .sidebar-toggle { left: calc(var(--sidebar-collapsed) - 14px); }
        .sidebar-toggle i { transition: transform 0.3s; }
        #sidebar.collapsed ~ .sidebar-toggle i { transform: rotate(180deg); }

        /* ===================== TOPBAR ===================== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: rgba(13, 15, 26, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 1030;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #sidebar.collapsed ~ * .topbar,
        .topbar.collapsed { left: var(--sidebar-collapsed); }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
        }

        /* Hamburger for mobile */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        /* ===================== MAIN CONTENT ===================== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            min-height: calc(100vh - var(--topbar-height));
            padding: 28px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .main-wrapper.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        /* ===================== CARDS ===================== */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 14px;
            padding: 16px 20px;
        }
        .card-body { padding: 20px; }

        /* ===================== TABLES ===================== */
        .table {
            color: var(--text-primary);
        }
        .table > :not(caption) > * > * {
            background-color: transparent;
            border-color: var(--border-color);
        }
        .table-hover > tbody > tr:hover > * {
            background-color: rgba(100, 108, 255, 0.05);
        }
        .table thead th {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 16px;
        }
        .table tbody td {
            padding: 12px 16px;
            font-size: 13.5px;
            vertical-align: middle;
        }

        /* ===================== BADGES & BUTTONS ===================== */
        .btn {
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
        }
        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }
        .btn-primary:hover {
            background: #5457ee;
            border-color: #5457ee;
            box-shadow: 0 0 16px var(--accent-glow);
        }

        /* ===================== STAT CARDS ===================== */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }
        .stat-card.accent-blue::before { background: linear-gradient(90deg, #646cff, #818cf8); }
        .stat-card.accent-green::before { background: linear-gradient(90deg, #22d3ee, #10b981); }
        .stat-card.accent-purple::before { background: linear-gradient(90deg, #a78bfa, #ec4899); }
        .stat-card.accent-orange::before { background: linear-gradient(90deg, #f59e0b, #f97316); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ===================== ALERTS ===================== */
        .alert {
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 13.5px;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.25);
            color: #86efac;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
            color: #fca5a5;
        }
        .alert-info {
            background: rgba(100, 108, 255, 0.1);
            border-color: rgba(100, 108, 255, 0.25);
            color: #a5b4fc;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.25);
            color: #fcd34d;
        }

        /* ===================== FORMS ===================== */
        .form-control, .form-select {
            background: rgba(30, 34, 54, 0.8);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 13.5px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(30, 34, 54, 0.9);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            color: var(--text-primary);
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        /* ===================== MODAL ===================== */
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }
        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }
        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        /* ===================== PAGINATION ===================== */
        .pagination .page-link {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-primary);
            font-size: 13px;
        }
        .pagination .page-link:hover {
            background: var(--sidebar-hover-bg);
            border-color: var(--accent);
            color: var(--accent);
        }
        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
        }

        /* ===================== MOBILE ===================== */
        @media (max-width: 768px) {
            #sidebar {
                left: -100%;
                width: var(--sidebar-width) !important;
                transition: left 0.3s;
            }
            #sidebar.mobile-open {
                left: 0;
            }
            .sidebar-toggle { display: none; }
            .mobile-menu-btn { display: block; }
            .topbar { left: 0 !important; }
            .main-wrapper { margin-left: 0 !important; padding: 16px; }
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.6);
                z-index: 1039;
                display: none;
            }
            .sidebar-overlay.active { display: block; }
        }

        /* ===================== IMPORT TOAST ===================== */
        .import-toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1055;
            width: 320px;
        }
        .import-toast-container .toast {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }
        .import-toast-container .toast-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        /* Breadcrumb */
        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted);
        }
        .breadcrumb-item a {
            color: var(--accent);
            text-decoration: none;
        }
        .breadcrumb-item.active { color: var(--text-muted); }

        /* Scrollbar global */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-main); }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ======================== SIDEBAR ======================== -->
<div id="sidebar">
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">A</div>
        <div class="sidebar-brand-text">
            <span class="brand-name">SAGAR Admin</span>
            <span class="brand-sub">School Management</span>
        </div>
    </a>

    <div class="sidebar-nav">
        @auth('admin')

        <div class="sidebar-section-label">Main Menu</div>

        <div class="nav-item-wrapper">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 sidebar-icon"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>
        </div>

        <div class="sidebar-section-label">Data Sekolah</div>

        <div class="nav-item-wrapper">
            <a href="{{ route('admin.jurusan.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3 sidebar-icon"></i>
                <span class="sidebar-label">Jurusan</span>
            </a>
        </div>

        <div class="nav-item-wrapper">
            <a href="{{ route('admin.kelas.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="bi bi-door-open sidebar-icon"></i>
                <span class="sidebar-label">Kelas</span>
            </a>
        </div>

        <div class="nav-item-wrapper">
            <a href="{{ route('admin.siswa.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                <i class="bi bi-people sidebar-icon"></i>
                <span class="sidebar-label">Siswa</span>
            </a>
        </div>

        <div class="nav-item-wrapper">
            <a href="{{ route('admin.ekskul.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.ekskul.*') ? 'active' : '' }}">
                <i class="bi bi-trophy sidebar-icon"></i>
                <span class="sidebar-label">Ekskul</span>
            </a>
        </div>

        @endauth
    </div>

    @auth('admin')
    <div class="sidebar-footer">
        <div class="nav-item-wrapper">
            <div class="sidebar-link" style="pointer-events:none; margin-bottom: 4px;">
                <i class="bi bi-person-circle sidebar-icon" style="color: var(--accent);"></i>
                <span class="sidebar-label" style="color: var(--text-muted); font-size: 12px;">
                    {{ auth('admin')->user()->name }}
                </span>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
            @csrf
            <button type="button" class="sidebar-link w-100 text-start"
                    style="color: #f87171; border:none; background:none;"
                    onclick="confirmLogout()">
                <i class="bi bi-box-arrow-left sidebar-icon"></i>
                <span class="sidebar-label">Logout</span>
            </button>
        </form>
    </div>
    @endauth
</div>

<!-- Sidebar toggle button (desktop) -->
<button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="bi bi-chevron-left" id="toggleIcon"></i>
</button>

<!-- ======================== TOPBAR ======================== -->
<div class="topbar" id="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="mobile-menu-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
        </div>
    </div>

    @auth('admin')
    <div class="topbar-right">
        <div class="topbar-avatar" title="{{ auth('admin')->user()->name }}">
            {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
        </div>
    </div>
    @endauth
</div>

<!-- ======================== MAIN CONTENT ======================== -->
<div class="main-wrapper" id="mainWrapper">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<!-- ======================== SCRIPTS ======================== -->
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ========= Sidebar Toggle =========
    const sidebar = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('mainWrapper');
    const topbar = document.getElementById('topbar');
    const toggleIcon = document.getElementById('toggleIcon');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = () => window.innerWidth <= 768;

    // Restore state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' && !isMobile()) {
        sidebar.classList.add('collapsed');
        mainWrapper.classList.add('sidebar-collapsed');
    }

    function toggleSidebar() {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
    }

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }
    });

    // ========= Confirm Logout =========
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah kamu yakin ingin keluar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#646cff',
            cancelButtonColor: '#374151',
            background: '#131624',
            color: '#e2e8f0',
            borderRadius: '16px',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }

    // ========= SweetAlert for flash success =========
    @if(session('success_swal'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success_swal') }}',
        timer: 2500,
        showConfirmButton: false,
        background: '#131624',
        color: '#e2e8f0',
        iconColor: '#22d3ee',
    });
    @endif
</script>

@auth('admin')
<div class="import-toast-container" id="importToastContainer"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('importToastContainer');
        let activeToasts = {};

        function checkImportStatus() {
            fetch('{{ route('admin.import.status') }}')
                .then(res => res.json())
                .then(data => {
                    const imports = data.imports || {};
                    for (const [id, statusData] of Object.entries(imports)) {
                        let toastEl = document.getElementById(`import-toast-${id}`);
                        let percentage = statusData.total > 0 ? Math.round((statusData.processed / statusData.total) * 100) : 0;
                        let statusText = 'Processing...';
                        let progressBarClass = 'bg-primary';

                        if (statusData.status === 'pending') {
                            statusText = 'Waiting in queue...';
                        } else if (statusData.status === 'completed') {
                            statusText = 'Selesai!';
                            progressBarClass = 'bg-success';
                            percentage = 100;
                        } else if (statusData.status === 'failed') {
                            statusText = 'Gagal: ' + (statusData.error || 'Unknown error');
                            progressBarClass = 'bg-danger';
                        }

                        if (!toastEl) {
                            toastEl = document.createElement('div');
                            toastEl.id = `import-toast-${id}`;
                            toastEl.className = 'toast show mb-2';
                            toastEl.innerHTML = `
                                <div class="toast-header">
                                    <i class="bi bi-upload me-2 text-primary"></i>
                                    <strong class="me-auto">Import Siswa</strong>
                                    <small class="text-muted status-text">${statusText}</small>
                                    <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
                                </div>
                                <div class="toast-body">
                                    <div class="progress mb-2" style="height: 6px; border-radius: 4px; background: rgba(255,255,255,0.1);">
                                        <div class="progress-bar progress-bar-animated ${progressBarClass}" style="width: ${percentage}%;"></div>
                                    </div>
                                    <div class="small text-muted text-center progress-counts">${statusData.processed} / ${statusData.total}</div>
                                </div>
                            `;
                            container.appendChild(toastEl);
                            activeToasts[id] = toastEl;
                        } else {
                            toastEl.querySelector('.status-text').innerText = statusText;
                            const pb = toastEl.querySelector('.progress-bar');
                            pb.style.width = percentage + '%';
                            pb.className = `progress-bar progress-bar-animated ${progressBarClass}`;
                            toastEl.querySelector('.progress-counts').innerText = `${statusData.processed} / ${statusData.total}`;
                            if (statusData.status === 'completed' || statusData.status === 'failed') {
                                pb.classList.remove('progress-bar-animated');
                                setTimeout(() => { toastEl.remove(); delete activeToasts[id]; }, 5000);
                            }
                        }
                    }

                    let hasActive = Object.values(imports).some(s => s.status === 'pending' || s.status === 'processing');
                    setTimeout(checkImportStatus, hasActive ? 3000 : 10000);
                })
                .catch(() => setTimeout(checkImportStatus, 10000));
        }

        checkImportStatus();
    });
</script>
@endauth

@stack('scripts')
</body>
</html>
