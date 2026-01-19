<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - @yield('title', 'Teacher Dashboard')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/duotone/style.css">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

    <style>
        :root {
            --pc-sidebar-width: 280px;
            --pc-header-height: 74px;
            --bs-body-bg: #f8fafc;
            --pc-sidebar-background: linear-gradient(180deg, #065f46 0%, #064e3b 100%);
            --pc-sidebar-color: rgba(255,255,255,0.7);
            --pc-sidebar-color-active: #ffffff;
            --pc-brand-color-1: #10b981;
            --pc-brand-color-2: #059669;
            --pc-accent-color: #10b981;
        }

        * {
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background: var(--bs-body-bg);
        }

        /* Sidebar Styles */
        .pc-sidebar {
            width: var(--pc-sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1025;
            background: var(--pc-sidebar-background);
            transition: all 0.3s ease;
            box-shadow: 0 0 30px rgba(0,0,0,0.15);
        }

        .pc-sidebar .navbar-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .pc-sidebar .m-header {
            height: var(--pc-header-height);
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .pc-sidebar .m-header .b-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .pc-sidebar .m-header .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--pc-brand-color-1) 0%, var(--pc-brand-color-2) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .pc-sidebar .navbar-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
        }

        .pc-sidebar .navbar-content::-webkit-scrollbar {
            width: 5px;
        }

        .pc-sidebar .navbar-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 3px;
        }

        .pc-sidebar .pc-navbar {
            list-style: none;
            padding: 0 16px;
            margin: 0;
        }

        .pc-sidebar .pc-item {
            margin-bottom: 4px;
        }

        .pc-sidebar .pc-item.pc-caption {
            padding: 18px 12px 10px;
        }

        .pc-sidebar .pc-item.pc-caption label {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .pc-sidebar .pc-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--pc-sidebar-color);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .pc-sidebar .pc-link:hover {
            background: rgba(255,255,255,0.08);
            color: var(--pc-sidebar-color-active);
        }

        .pc-sidebar .pc-item.active .pc-link {
            background: linear-gradient(135deg, var(--pc-brand-color-1) 0%, var(--pc-brand-color-2) 100%);
            color: var(--pc-sidebar-color-active);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .pc-sidebar .pc-micon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pc-sidebar .pc-micon i {
            font-size: 20px;
        }

        .pc-sidebar .pc-mtext {
            font-size: 14px;
        }

        .pc-sidebar .pc-badge {
            margin-left: auto;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* User Card in Sidebar */
        .pc-user-card {
            margin: 16px;
            background: rgba(255,255,255,0.08);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .pc-user-card .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
        }

        /* Header Styles */
        .pc-header {
            position: fixed;
            top: 0;
            left: var(--pc-sidebar-width);
            right: 0;
            height: var(--pc-header-height);
            background: #ffffff;
            z-index: 1024;
            box-shadow: 0 1px 0 rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .pc-header .header-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 24px;
        }

        .pc-header .pc-head-link {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #64748b;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .pc-header .pc-head-link:hover {
            background: #f1f5f9;
            color: var(--pc-brand-color-1);
        }

        .pc-header .pc-head-link i {
            font-size: 22px;
        }

        .pc-header .dropdown-toggle::after {
            display: none;
        }

        .pc-header .pc-h-item {
            margin: 0 4px;
        }

        .pc-header .user-avatar-header {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
        }

        /* Container/Content Styles */
        .pc-container {
            position: relative;
            margin-left: var(--pc-sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .pc-content {
            padding: calc(var(--pc-header-height) + 24px) 24px 24px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header .page-header-title h5 {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .page-header .breadcrumb {
            padding: 0;
            margin: 8px 0 0;
            background: transparent;
        }

        .page-header .breadcrumb-item {
            font-size: 13px;
        }

        .page-header .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }

        .page-header .breadcrumb-item a:hover {
            color: var(--pc-brand-color-1);
        }

        .page-header .breadcrumb-item.active {
            color: #94a3b8;
        }

        .page-header .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #cbd5e1;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
        }

        .card-header .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            background: transparent;
            border-top: 1px solid #f1f5f9;
            padding: 16px 24px;
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            padding: 14px 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table-card .card-body {
            padding: 0;
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .badge-soft-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge-soft-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .badge-soft-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .badge-soft-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .badge-soft-secondary {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .bg-light-success {
            background: rgba(16, 185, 129, 0.12) !important;
            color: #059669 !important;
        }

        .bg-light-warning {
            background: rgba(245, 158, 11, 0.12) !important;
            color: #d97706 !important;
        }

        .bg-light-danger {
            background: rgba(239, 68, 68, 0.12) !important;
            color: #dc2626 !important;
        }

        .bg-light-info {
            background: rgba(59, 130, 246, 0.12) !important;
            color: #2563eb !important;
        }

        .bg-light-primary {
            background: rgba(16, 185, 129, 0.12) !important;
            color: #059669 !important;
        }

        .bg-light-secondary {
            background: rgba(100, 116, 139, 0.12) !important;
            color: #64748b !important;
        }

        /* Button Styles */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pc-brand-color-1) 0%, var(--pc-brand-color-2) 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .btn-outline-primary {
            border-color: var(--pc-brand-color-1);
            color: var(--pc-brand-color-1);
        }

        .btn-outline-primary:hover {
            background: var(--pc-brand-color-1);
            border-color: var(--pc-brand-color-1);
            color: #fff;
        }

        .btn-light-primary {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: none;
        }

        .btn-light-primary:hover {
            background: rgba(16, 185, 129, 0.2);
            color: #047857;
        }

        .btn-light-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: none;
        }

        .btn-light-warning:hover {
            background: rgba(245, 158, 11, 0.2);
            color: #b45309;
        }

        .btn-light-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: none;
        }

        .btn-light-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #b91c1c;
        }

        .btn-light-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border: none;
        }

        .btn-light-info:hover {
            background: rgba(59, 130, 246, 0.2);
            color: #1d4ed8;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Form Styles */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--pc-brand-color-1);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-check-input:checked {
            background-color: var(--pc-brand-color-1);
            border-color: var(--pc-brand-color-1);
        }

        /* Stats Card */
        .stats-card {
            position: relative;
            overflow: hidden;
        }

        .stats-card .stats-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            opacity: 0.15;
        }

        .stats-card .stats-icon i {
            font-size: 50px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state-icon i {
            font-size: 48px;
            color: #94a3b8;
        }

        /* Alert/Toast */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        /* Dropdown */
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            color: #475569;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .dropdown-item i {
            margin-right: 10px;
            font-size: 18px;
        }

        /* Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pc-sidebar {
                left: calc(var(--pc-sidebar-width) * -1);
            }

            .pc-sidebar.mob-sidebar-active {
                left: 0;
            }

            .pc-header {
                left: 0;
            }

            .pc-container {
                margin-left: 0;
            }

            .pc-sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1024;
                display: none;
            }

            .pc-sidebar-overlay.active {
                display: block;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Pagination */
        .pagination {
            gap: 4px;
        }

        .page-link {
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            color: #64748b;
            font-weight: 500;
        }

        .page-link:hover {
            background: #f1f5f9;
            color: var(--pc-brand-color-1);
        }

        .page-item.active .page-link {
            background: var(--pc-brand-color-1);
            color: #fff;
        }

        /* List Group */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 16px;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* Avatar */
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-weight: 600;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .avatar-md {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }

        .avatar-lg {
            width: 64px;
            height: 64px;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="pc-sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav class="pc-sidebar" id="pc-sidebar">
        <div class="navbar-wrapper">
            <!-- Logo -->
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand">
                    <span class="logo-icon">
                        <i class="ph ph-chalkboard-teacher text-white f-22"></i>
                    </span>
                    <span class="logo-text">ZAFProctor</span>
                </a>
            </div>

            <!-- Sidebar Content -->
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <!-- Navigation Caption -->
                    <li class="pc-item pc-caption">
                        <label>Menu Utama</label>
                    </li>

                    <!-- Dashboard -->
                    <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ph-duotone ph-chart-pie-slice"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <!-- Exam Management -->
                    <li class="pc-item pc-caption">
                        <label>Manajemen Ujian</label>
                    </li>

                    <!-- All Exams -->
                    <li class="pc-item {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}">
                        <a href="{{ route('teacher.exams.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ph-duotone ph-exam"></i></span>
                            <span class="pc-mtext">Kelola Ujian</span>
                        </a>
                    </li>

                    <!-- Courses -->
                    <li class="pc-item pc-caption">
                        <label>Mata Pelajaran</label>
                    </li>

                    @php
                        $teacherCourses = auth()->user()->taughtCourses ?? collect();
                    @endphp
                    @foreach($teacherCourses->take(5) as $course)
                        <li class="pc-item">
                            <a href="{{ route('teacher.exams.index', ['course' => $course->id]) }}" class="pc-link">
                                <span class="pc-micon"><i class="ph-duotone ph-book-open"></i></span>
                                <span class="pc-mtext">{{ Str::limit($course->name, 20) }}</span>
                            </a>
                        </li>
                    @endforeach

                    <!-- Settings -->
                    <li class="pc-item pc-caption">
                        <label>Pengaturan</label>
                    </li>

                    <!-- Profile -->
                    <li class="pc-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}" class="pc-link">
                            <span class="pc-micon"><i class="ph-duotone ph-user-circle"></i></span>
                            <span class="pc-mtext">Profil Saya</span>
                        </a>
                    </li>
                </ul>

                <!-- User Card -->
                <div class="card pc-user-card mt-auto">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar me-3">
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 text-white text-truncate">{{ auth()->user()->name }}</h6>
                                <small class="text-white-50">Guru</small>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm w-100">
                                <i class="ph-duotone ph-sign-out me-2"></i>Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="pc-header">
        <div class="header-wrapper">
            <!-- Mobile Menu Toggle -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled d-flex align-items-center mb-0">
                    <li class="pc-h-item d-lg-none">
                        <a href="#" class="pc-head-link" id="mobile-collapse">
                            <i class="ph ph-list"></i>
                        </a>
                    </li>
                    <li class="pc-h-item d-none d-lg-block">
                        <div class="text-muted">
                            <span>Selamat datang,</span>
                            <strong class="text-dark">{{ auth()->user()->name }}</strong>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Right Side -->
            <div class="ms-auto">
                <ul class="list-unstyled d-flex align-items-center mb-0">
                    <!-- Create Exam Button -->
                    <li class="pc-h-item me-2">
                        <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary btn-sm">
                            <i class="ph-duotone ph-plus me-1"></i>Buat Ujian
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="pc-h-item">
                        <div class="dropdown">
                            <a href="#" class="pc-head-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ph ph-bell"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                                <div class="p-3 border-bottom">
                                    <h6 class="mb-0">Notifikasi</h6>
                                </div>
                                <div class="p-3 text-center text-muted">
                                    <i class="ph ph-bell-slash" style="font-size: 32px;"></i>
                                    <p class="mb-0 mt-2">Tidak ada notifikasi baru</p>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- User Menu -->
                    <li class="pc-h-item">
                        <div class="dropdown">
                            <a href="#" class="pc-head-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar-header">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="px-3 py-2 border-bottom">
                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="ph-duotone ph-user"></i>Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ph-duotone ph-sign-out"></i>Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ph-duotone ph-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ph-duotone ph-x-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ph-duotone ph-warning me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="pc-footer" style="margin-left: var(--pc-sidebar-width); padding: 16px 24px; border-top: 1px solid #e2e8f0;">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0 text-muted small">{{ date('Y') }} © ZAFProctor - Sistem Ujian Online dengan Proctoring</p>
            <p class="mb-0 text-muted small">v1.0.0</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        document.getElementById('mobile-collapse')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('pc-sidebar').classList.toggle('mob-sidebar-active');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        });

        document.getElementById('sidebar-overlay')?.addEventListener('click', function() {
            document.getElementById('pc-sidebar').classList.remove('mob-sidebar-active');
            this.classList.remove('active');
        });

        // Auto-hide alerts
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
