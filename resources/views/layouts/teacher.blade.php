<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - @yield('title', 'Teacher Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">

    <!-- App Design System -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --pc-sidebar-width: 272px;
            --pc-header-height: 70px;
            --bs-body-bg: #f5f7f5;
            --pc-sidebar-background: #082f1a;
            --pc-sidebar-color: rgba(255,255,255,0.6);
            --pc-sidebar-color-active: #ffffff;
            --pc-brand-color-1: #10b981;
            --pc-brand-color-2: #059669;
            --pc-accent-color: #10b981;
        }

        * {
            font-family: 'Manrope', sans-serif;
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
            border-right: 1px solid rgba(255,255,255,0.04);
        }

        .pc-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(ellipse at 0% 100%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 100% 0%, rgba(52, 211, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .pc-sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.012) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .pc-sidebar .navbar-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .pc-sidebar .m-header {
            height: var(--pc-header-height);
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
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
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--pc-brand-color-1) 0%, #34d399 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
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
            background: rgba(16, 185, 129, 0.12);
            color: var(--pc-sidebar-color-active);
            font-weight: 600;
            border-left: 3px solid var(--pc-brand-color-1);
            padding-left: 13px;
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
        .pc-sidebar .pc-user-card {
            margin: 16px;
            background: rgba(255,255,255,0.12) !important;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.12);
        }

        .pc-sidebar .pc-user-card .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
        }

        /* Header Styles */
        .pc-header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--pc-sidebar-width);
            height: var(--pc-header-height);
            background: #ffffff;
            z-index: 1024;
            box-shadow: 0 1px 0 rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            border-bottom: 1px solid #e8eaed;
        }

        .pc-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--pc-brand-color-1), #34d399, #6ee7b7);
            opacity: 0.6;
        }

        .pc-header .header-wrapper {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0 24px;
        }

        .pc-header .pc-head-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            color: #64748b;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pc-header .pc-head-link:hover {
            background: #f1f5f9;
            color: var(--pc-brand-color-1);
        }

        .pc-header .pc-h-item {
            list-style: none;
            margin: 0 4px;
        }

        .pc-header .pc-h-badge {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 9px;
            padding: 4px 6px;
        }

        /* Container/Content Styles */
        .pc-container {
            position: relative;
            margin-left: var(--pc-sidebar-width);
            padding-top: var(--pc-header-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .pc-content {
            padding: 24px;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 24px;
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
        }

        .card-header .card-title {
            font-size: 16px;
            font-weight: 600;
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

        .badge-soft-primary {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
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

        /* Utility Classes */
        .arrow-none::after { display: none; }

        .f-w-300 { font-weight: 300; }
        .f-w-400 { font-weight: 400; }
        .f-w-500 { font-weight: 500; }
        .f-w-600 { font-weight: 600; }
        .f-w-700 { font-weight: 700; }

        .f-10 { font-size: 10px; }
        .f-12 { font-size: 12px; }
        .f-14 { font-size: 14px; }
        .f-16 { font-size: 16px; }
        .f-20 { font-size: 20px; }
        .f-24 { font-size: 24px; }
        .f-30 { font-size: 30px; }
        .f-36 { font-size: 36px; }

        .m-b-0 { margin-bottom: 0; }
        .m-b-10 { margin-bottom: 10px; }
        .m-b-15 { margin-bottom: 15px; }
        .m-b-20 { margin-bottom: 20px; }
        .m-t-10 { margin-top: 10px; }
        .m-t-15 { margin-top: 15px; }
        .m-t-20 { margin-top: 20px; }
        .m-t-30 { margin-top: 30px; }
        .m-r-5 { margin-right: 5px; }
        .m-r-10 { margin-right: 10px; }
        .m-r-15 { margin-right: 15px; }
        .m-l-10 { margin-left: 10px; }
        .m-l-15 { margin-left: 15px; }

        /* Button Styles */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pc-brand-color-1) 0%, #34d399 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            position: relative;
            overflow: hidden;
        }

        .text-primary { color: var(--pc-brand-color-1) !important; }
        .bg-primary { background-color: var(--pc-brand-color-1) !important; }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::after {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar i {
            font-size: 20px;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
        }

        .avatar-sm i {
            font-size: 16px;
        }

        .avatar-md {
            width: 48px;
            height: 48px;
        }

        .avatar-md i {
            font-size: 24px;
        }

        .avatar-lg {
            width: 64px;
            height: 64px;
        }

        .avatar-lg i {
            font-size: 30px;
        }

        /* Alpine.js cloak */
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="zaf-ui role-teacher">
    <!-- Sidebar Overlay -->
    <div class="pc-sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav class="pc-sidebar" id="pc-sidebar">
        <div class="navbar-wrapper">
            <!-- Logo -->
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand">
                    <span class="logo-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="white" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.18l6.2 3.45v2.3L12 13.36 5.8 9.93v-2.3L12 4.18zM5.8 11.64L12 15.05l6.2-3.41v4.73L12 19.82l-6.2-3.45v-4.73z"/>
                        </svg>
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
                            <span class="pc-micon"><i class="ph ph-chart-pie-slice"></i></span>
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
                            <span class="pc-micon"><i class="ph ph-file-text"></i></span>
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
                                <span class="pc-micon"><i class="ph ph-book-open"></i></span>
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
                            <span class="pc-micon"><i class="ph ph-user-circle"></i></span>
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
                                <i class="ph ph-sign-out me-2"></i>Keluar
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
                        <div class="page-header-title">
                            <h5 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h5>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Right Side -->
            <div class="ms-auto">
                <ul class="list-unstyled d-flex align-items-center mb-0">
                    <!-- Date Display -->
                    <li class="pc-h-item d-none d-md-block me-2">
                        <span class="badge bg-light text-dark px-3 py-2 f-14">
                            <i class="ph ph-calendar-dots me-1"></i>
                            {{ now()->locale('id')->translatedFormat('l, d M Y') }}
                        </span>
                    </li>

                    <!-- Notifications -->
                    <li class="pc-h-item dropdown me-2">
                        <a class="pc-head-link dropdown-toggle arrow-none position-relative" data-bs-toggle="dropdown" href="#" role="button">
                            <i class="ph ph-bell"></i>
                            @php
                                $pendingGrading = \App\Models\ExamAttempt::query()
                                    ->where('status', \App\Models\ExamAttempt::STATUS_SUBMITTED)
                                    ->whereHas('exam', fn($q) => $q->where('created_by', auth()->id()))
                                    ->count();
                            @endphp
                            @if($pendingGrading > 0)
                                <span class="badge bg-danger rounded-circle position-absolute" style="top: 2px; right: 2px; font-size: 10px; padding: 4px 6px;">{{ $pendingGrading }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                            <div class="px-3 py-2 border-bottom">
                                <h6 class="mb-0">Notifikasi</h6>
                            </div>
                            @if($pendingGrading > 0)
                                <a class="dropdown-item py-3" href="{{ route('teacher.exams.index', ['pending_grading' => 1, 'from_notification' => 1]) }}">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-warning p-2 rounded">
                                                <i class="ph ph-pencil-simple f-20"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 f-14">Menunggu Penilaian</h6>
                                            <p class="mb-0 text-muted f-12">{{ $pendingGrading }} jawaban perlu dinilai</p>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="p-4 text-center text-muted">
                                    <i class="ph ph-check-circle f-30 mb-2 d-block"></i>
                                    <span class="f-14">Tidak ada notifikasi</span>
                                </div>
                            @endif
                        </div>
                    </li>

                    <!-- Profile Dropdown -->
                    <li class="pc-h-item dropdown">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="px-3 py-2 border-bottom mb-2">
                                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="ph ph-user-circle me-2"></i>Profil Saya
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="ph ph-sign-out me-2"></i>Keluar
                                </button>
                            </form>
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
                    <i class="ph ph-check-circle me-2 f-20"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ph ph-x-circle me-2 f-20"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ph ph-warning me-2"></i>
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
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
