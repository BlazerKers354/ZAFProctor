<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $attempt->exam->title }} - {{ config('app.name', 'ZAFProctor') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Custom Exam Styles -->
    <style>
        :root {
            --pc-sidebar-width: 280px;
            --pc-header-height: 70px;
            --exam-primary: #7c3aed;
            --exam-success: #2ca87f;
            --exam-warning: #e58a00;
            --exam-danger: #dc2626;
            --exam-dark: #1c232f;
            --exam-light: #f8f9fa;
        }

        * {
            font-family: 'Open Sans', sans-serif;
            @if($attempt->exam->settings?->detect_copy_paste)
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            @endif
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            min-height: 100vh;
            overflow: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--exam-primary); border-radius: 10px; opacity: 0.5; }

        /* Header */
        .exam-header {
            background: linear-gradient(135deg, var(--exam-dark) 0%, #111827 100%);
            height: var(--pc-header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* Sidebar */
        .exam-sidebar {
            position: fixed;
            left: 0;
            top: var(--pc-header-height);
            width: var(--pc-sidebar-width);
            height: calc(100vh - var(--pc-header-height));
            background: linear-gradient(180deg, var(--exam-dark) 0%, #111827 100%);
            border-right: 1px solid rgba(255,255,255,0.05);
            overflow-y: auto;
            z-index: 1020;
        }

        /* Main Content */
        .exam-content {
            margin-left: var(--pc-sidebar-width);
            margin-top: var(--pc-header-height);
            padding: 24px;
            min-height: calc(100vh - var(--pc-header-height));
            overflow-y: auto;
            max-height: calc(100vh - var(--pc-header-height));
        }

        /* Camera Container */
        .camera-box {
            width: 100px;
            height: 70px;
            border-radius: 10px;
            overflow: hidden;
            background: #374151;
            position: relative;
            border: 2px solid rgba(255,255,255,0.1);
        }
        .camera-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }
        .camera-box canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            transform: scaleX(-1);
        }
        .camera-status {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .camera-status.active { background: var(--exam-success); }
        .camera-status.inactive { background: var(--exam-danger); }
        .camera-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #374151;
            color: #9ca3af;
        }

        /* Timer Box */
        .timer-box {
            background: rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 8px 16px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .timer-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: 2px;
        }
        .timer-box.warning {
            background: linear-gradient(135deg, var(--exam-danger) 0%, #b91c1c 100%);
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        /* Violation Badge */
        .violation-badge {
            background: linear-gradient(135deg, var(--exam-danger) 0%, #b91c1c 100%);
            border-radius: 12px;
            padding: 8px 14px;
            display: none;
        }
        .violation-badge.show { display: flex; }

        /* Question Navigation */
        .question-nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .q-nav-btn {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(255,255,255,0.08);
            color: #9ca3af;
        }
        .q-nav-btn:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        .q-nav-btn.answered {
            background: linear-gradient(135deg, var(--exam-success) 0%, #22c55e 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(44, 168, 127, 0.3);
        }
        .q-nav-btn.current {
            box-shadow: 0 0 0 2px #fff;
        }

        /* Legend */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .legend-dot {
            width: 20px;
            height: 20px;
            border-radius: 6px;
        }
        .legend-dot.answered { background: linear-gradient(135deg, var(--exam-success) 0%, #22c55e 100%); }
        .legend-dot.unanswered { background: rgba(255,255,255,0.08); }
        .legend-dot.current { background: rgba(255,255,255,0.08); box-shadow: 0 0 0 2px #fff; }

        /* Progress Card */
        .progress-card {
            background: linear-gradient(135deg, var(--exam-primary) 0%, #6d28d9 100%);
            border-radius: 16px;
            padding: 20px;
            color: #fff;
        }
        .progress-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }
        .progress-label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Question Card */
        .question-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .question-header {
            background: linear-gradient(135deg, var(--exam-dark) 0%, #1f2937 100%);
            padding: 20px 24px;
            color: #fff;
        }
        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .question-points {
            background: rgba(70, 128, 255, 0.2);
            color: #93c5fd;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .question-body {
            padding: 28px;
        }
        .question-text {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 24px;
        }

        /* Option Cards */
        .option-item {
            display: flex;
            align-items: flex-start;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }
        .option-item:hover {
            border-color: var(--exam-primary);
            background: linear-gradient(135deg, rgba(70, 128, 255, 0.03) 0%, rgba(99, 102, 241, 0.03) 100%);
            transform: translateX(4px);
        }
        .option-item.selected {
            border-color: var(--exam-primary);
            background: linear-gradient(135deg, rgba(70, 128, 255, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
        }
        .option-letter {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6b7280;
            margin-right: 16px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .option-item.selected .option-letter {
            background: var(--exam-primary);
            border-color: var(--exam-primary);
            color: #fff;
        }
        .option-text {
            font-size: 1rem;
            color: #374151;
            line-height: 1.6;
            padding-top: 6px;
        }

        /* Essay Textarea */
        .essay-textarea {
            width: 100%;
            min-height: 200px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 20px;
            font-size: 1rem;
            line-height: 1.7;
            resize: vertical;
            transition: all 0.2s ease;
        }
        .essay-textarea:focus {
            outline: none;
            border-color: var(--exam-primary);
            box-shadow: 0 0 0 4px rgba(70, 128, 255, 0.1);
        }

        /* Navigation Buttons */
        .nav-footer {
            background: #f9fafb;
            padding: 20px 28px;
            border-top: 1px solid #e5e7eb;
        }
        .btn-nav {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-nav-outline {
            background: #fff;
            border: 2px solid #d1d5db;
            color: #374151;
        }
        .btn-nav-outline:hover {
            border-color: #9ca3af;
            background: #f9fafb;
        }
        .btn-nav-primary {
            background: linear-gradient(135deg, var(--exam-primary) 0%, #6d28d9 100%);
            border: none;
            color: #fff;
        }
        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(70, 128, 255, 0.3);
            color: #fff;
        }
        .btn-nav-success {
            background: linear-gradient(135deg, var(--exam-success) 0%, #22c55e 100%);
            border: none;
            color: #fff;
        }
        .btn-nav-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 168, 127, 0.3);
            color: #fff;
        }

        /* Warning Banner */
        .warning-banner {
            position: fixed;
            top: var(--pc-header-height);
            left: var(--pc-sidebar-width);
            right: 0;
            background: linear-gradient(135deg, var(--exam-danger) 0%, #b91c1c 100%);
            color: #fff;
            padding: 12px 20px;
            text-align: center;
            z-index: 1025;
            display: none;
            animation: slideDown 0.3s ease;
        }
        .warning-banner.show { display: block; }
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Modals */
        .exam-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .exam-modal-backdrop.show { display: flex; }
        .exam-modal {
            background: #fff;
            border-radius: 24px;
            max-width: 450px;
            width: 90%;
            overflow: hidden;
            animation: modalIn 0.3s ease;
        }
        @keyframes modalIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .exam-modal-header {
            padding: 28px;
            text-align: center;
        }
        .exam-modal-header.success { background: linear-gradient(135deg, var(--exam-success) 0%, #22c55e 100%); }
        .exam-modal-header.warning { background: linear-gradient(135deg, var(--exam-warning) 0%, #f59e0b 100%); }
        .exam-modal-header.danger { background: linear-gradient(135deg, var(--exam-danger) 0%, #b91c1c 100%); }
        .exam-modal-header.primary { background: linear-gradient(135deg, var(--exam-primary) 0%, #6d28d9 100%); }
        .exam-modal-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: #fff;
        }
        .exam-modal-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }
        .exam-modal-body {
            padding: 24px 28px;
        }
        .exam-modal-footer {
            padding: 0 28px 24px;
            display: flex;
            gap: 12px;
        }

        /* Submit Summary */
        .submit-summary {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .summary-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--exam-dark);
        }

        /* Hidden on load */
        .question-panel { display: none; }
        .question-panel.active { display: block; }

        /* Face Modals */
        .face-modal { z-index: 1050; }

        /* ── Flag / Bookmark ─────────────────── */
        .q-nav-btn.flagged {
            position: relative;
        }
        .q-nav-btn.flagged::after {
            content: '';
            position: absolute;
            top: 3px;
            right: 3px;
            width: 8px;
            height: 8px;
            background: #f59e0b;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.2);
            box-shadow: 0 0 4px rgba(245,158,11,0.5);
        }
        .btn-flag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 500;
            border: 1px solid rgba(245,158,11,0.3);
            background: transparent;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-flag:hover, .btn-flag.active {
            background: rgba(245,158,11,0.15);
            color: #f59e0b;
            border-color: #f59e0b;
        }
        .legend-dot.flagged {
            background: #f59e0b;
            box-shadow: 0 0 6px rgba(245,158,11,0.4);
        }

        /* ── Dark Mode ───────────────────────── */
        body.dark-mode .question-card { background: #1e1e2e; border-color: #2a2a3d; }
        body.dark-mode .question-body { color: #e0e0e0; }
        body.dark-mode .question-text { color: #e0e0e0; }
        body.dark-mode .option-item { background: #252536; border-color: #333; color: #d0d0d0; }
        body.dark-mode .option-item:hover { background: #2d2d44; }
        body.dark-mode .option-item.selected { background: rgba(99,102,241,0.15); border-color: var(--exam-primary); }
        body.dark-mode .essay-textarea { background: #252536; border-color: #333; color: #e0e0e0; }
        body.dark-mode .nav-footer { background: #1a1a2a; border-color: #2a2a3d; }
        body.dark-mode .exam-content { background: #141421; }

        /* ── Font Size ───────────────────────── */
        body.fs-small .question-text { font-size: 0.85rem; }
        body.fs-small .option-text { font-size: 0.82rem; }
        body.fs-small .essay-textarea { font-size: 0.85rem; }
        body.fs-large .question-text { font-size: 1.15rem; }
        body.fs-large .option-text { font-size: 1.05rem; }
        body.fs-large .essay-textarea { font-size: 1.1rem; }

        /* ── Toolbar buttons ─────────────────── */
        .exam-toolbar-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            color: #aaa;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .exam-toolbar-btn:hover, .exam-toolbar-btn.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-color: rgba(255,255,255,0.3);
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 3px;
            margin-left: 10px;
        }

        /* ── Prominent Progress ──────────────── */
        .header-progress-wrap {
            height: 6px;
            background: rgba(255,255,255,0.1);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .header-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--exam-success) 0%, #22c55e 100%);
            transition: width 0.5s ease;
            border-radius: 0 3px 3px 0;
        }
        .header-progress-text {
            position: absolute;
            bottom: 8px;
            right: 12px;
            font-size: 0.65rem;
            color: rgba(255,255,255,0.7);
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .exam-sidebar { width: 240px; }
            .exam-content { margin-left: 240px; }
            :root { --pc-sidebar-width: 240px; }
        }
        @media (max-width: 767px) {
            .exam-sidebar { 
                transform: translateX(-100%); 
                width: 280px;
                transition: transform 0.3s ease;
            }
            .exam-sidebar.show { transform: translateX(0); }
            .exam-content { margin-left: 0; }
            .warning-banner { left: 0; }
        }
    </style>
</head>
<body @if($attempt->exam->settings?->detect_right_click) oncontextmenu="return false;" @endif>

    <!-- Header -->
    <header class="exam-header">
        <div class="container-fluid h-100">
            <div class="d-flex align-items-center justify-content-between h-100 px-3">
                <!-- Left: Exam Info -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-white d-lg-none p-0 me-2" onclick="toggleSidebar()">
                        <i class="ph ph-list fs-4"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--exam-primary) 0%, #6d28d9 100%);">
                        <i class="ph ph-file-text text-white fs-5"></i>
                    </div>
                    <div class="d-none d-sm-block">
                        <h1 class="text-white mb-0 fw-semibold" style="font-size: 1.1rem;">{{ $attempt->exam->title }}</h1>
                        <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8rem;">
                            <i class="ph ph-book-open"></i>
                            <span>{{ $attempt->exam->course?->name ?? 'Ujian Umum' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Controls -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Connection Status Indicator -->
                    <div id="connection-indicator" class="d-flex align-items-center gap-1 px-2 py-1 rounded-2" style="background: rgba(44, 168, 127, 0.2);">
                        <div id="connection-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></div>
                        <span id="connection-text" class="text-white" style="font-size: 0.7rem;">Online</span>
                    </div>
                    
                    <!-- Camera Preview -->
                    <div class="camera-box d-none d-md-block">
                        <video id="camera-preview" autoplay muted playsinline></video>
                        <canvas id="face-canvas"></canvas>
                        <div id="camera-status" class="camera-status inactive"></div>
                        <div id="camera-placeholder" class="camera-placeholder">
                            <i class="ph ph-video-camera-slash fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Timer -->
                    <div id="timer-box" class="timer-box d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 36px; height: 36px; background: rgba(251, 191, 36, 0.15);">
                            <i class="ph ph-clock text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.7rem; line-height: 1;">Sisa Waktu</div>
                            <span id="timer-display" class="timer-display">--:--</span>
                        </div>
                    </div>
                    
                    <!-- Violation Counter -->
                    <div id="violation-badge" class="violation-badge align-items-center gap-2">
                        <i class="ph ph-warning text-white fs-6"></i>
                        <div>
                            <div class="text-white" style="font-size: 0.65rem; opacity: 0.8; line-height: 1;">Pelanggaran</div>
                            <span id="violation-count" class="text-white fw-bold" style="font-size: 1.1rem;">0</span>
                        </div>
                    </div>

                    <!-- Toolbar: Font Size -->
                    <div class="toolbar-group d-none d-md-flex">
                        <button onclick="setFontSize('small')" class="exam-toolbar-btn" title="Font kecil" id="fs-small">A<small style="font-size:0.6em">−</small></button>
                        <button onclick="setFontSize('normal')" class="exam-toolbar-btn active" title="Font normal" id="fs-normal">A</button>
                        <button onclick="setFontSize('large')" class="exam-toolbar-btn" title="Font besar" id="fs-large">A<small style="font-size:0.6em">+</small></button>
                    </div>

                    <!-- Toolbar: Dark Mode -->
                    <button onclick="toggleDarkMode()" class="exam-toolbar-btn d-none d-md-flex" title="Mode gelap" id="dark-mode-btn">
                        <i class="ph ph-moon"></i>
                    </button>
                    
                    <!-- Submit Button -->
                    <button onclick="confirmSubmit()" class="btn btn-nav-success d-none d-sm-inline-flex">
                        <i class="ph ph-paper-plane-tilt"></i>
                        <span>Kumpulkan</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar (prominent) -->
        <div class="header-progress-wrap">
            <div id="progress-bar" class="header-progress-fill"></div>
        </div>
        <span id="header-progress-text" class="header-progress-text">0%</span>
    </header>

    <!-- Warning Banner -->
    <div id="warning-banner" class="warning-banner">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <i class="ph ph-warning fs-5"></i>
            <span id="warning-message" class="fw-semibold">Peringatan!</span>
        </div>
    </div>

    <!-- Sidebar -->
    <aside id="exam-sidebar" class="exam-sidebar">
        <div class="p-4">
            <!-- Section Title -->
            <div class="mb-4">
                <h6 class="text-white fw-semibold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Navigasi Soal</h6>
                <p class="text-secondary mb-0" style="font-size: 0.75rem;">Klik nomor untuk pindah soal</p>
            </div>
            
            <!-- Question Navigation Grid -->
            <div class="question-nav-grid mb-4" id="question-nav">
                @foreach($questions as $index => $question)
                    <button onclick="goToQuestion({{ $index }})"
                            id="nav-btn-{{ $index }}"
                            class="q-nav-btn {{ isset($answeredQuestions[$question->id]) ? 'answered' : '' }} {{ $index === 0 ? 'current' : '' }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
            
            <!-- Legend -->
            <div class="mb-4 p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                <h6 class="text-white mb-3 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Keterangan</h6>
                <div class="legend-item">
                    <div class="legend-dot answered"></div>
                    <span class="text-secondary" style="font-size: 0.8rem;">Sudah dijawab</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot unanswered"></div>
                    <span class="text-secondary" style="font-size: 0.8rem;">Belum dijawab</span>
                </div>
                <div class="legend-item mb-0">
                    <div class="legend-dot current"></div>
                    <span class="text-secondary" style="font-size: 0.8rem;">Soal aktif</span>
                </div>
                <div class="legend-item mb-0 mt-2">
                    <div class="legend-dot flagged"></div>
                    <span class="text-secondary" style="font-size: 0.8rem;">Ditandai</span>
                </div>
            </div>
            
            <!-- Progress Card -->
            <div class="progress-card">
                <div class="progress-label mb-2">Progress Ujian</div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <span id="answered-count" class="progress-value">{{ count($answeredQuestions) }}</span>
                        <span style="font-size: 1.25rem; opacity: 0.7;">/{{ $questions->count() }}</span>
                    </div>
                    <div class="text-end">
                        <div class="progress-label">Terjawab</div>
                        <div id="answered-percent" class="fw-bold" style="font-size: 1.1rem;">{{ $questions->count() > 0 ? round((count($answeredQuestions) / $questions->count()) * 100) : 0 }}%</div>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.2); border-radius: 10px;">
                    <div id="progress-bar-sidebar" class="progress-bar" role="progressbar" style="width: {{ $questions->count() > 0 ? round((count($answeredQuestions) / $questions->count()) * 100) : 0 }}%; background: #fff; border-radius: 10px;"></div>
                </div>
            </div>
            
            <!-- Mobile Submit -->
            <button onclick="confirmSubmit()" class="btn btn-nav-success w-100 mt-4 d-sm-none">
                <i class="ph ph-paper-plane-tilt me-2"></i>
                Kumpulkan Ujian
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="exam-content">
        <div class="container-fluid px-0">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10 col-xxl-9">
                    <!-- Questions Container -->
                    <div id="questions-container">
                        @foreach($questions as $index => $question)
                            <div id="question-{{ $index }}" class="question-panel {{ $index === 0 ? 'active' : '' }}">
                                <div class="question-card">
                                    <!-- Question Header -->
                                    <div class="question-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="question-number">{{ $index + 1 }}</div>
                                                <div>
                                                    <span class="text-white fw-semibold">Soal {{ $index + 1 }}</span>
                                                    <span class="text-secondary"> dari {{ $questions->count() }}</span>
                                                </div>
                                            </div>
                                            <div class="question-points">
                                                <i class="ph ph-star me-1"></i>{{ $question->points }} poin
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Question Body -->
                                    <div class="question-body">
                                        <!-- Question Text -->
                                        <div class="question-text">
                                            {!! nl2br(e($question->question)) !!}
                                        </div>
                                        
                                        @if($question->question_image)
                                            <div class="mb-4">
                                                <img src="{{ asset('storage/' . $question->question_image) }}" 
                                                     alt="Gambar Soal" 
                                                     class="img-fluid rounded-3" style="max-width: 500px;">
                                            </div>
                                        @endif
                                        
                                        <!-- Options or Essay -->
                                        @if($question->isMultipleChoice())
                                            <div class="options-list">
                                                @foreach($question->options as $option)
                                                    <div class="option-item {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'selected' : '' }}"
                                                         onclick="selectOption({{ $question->id }}, {{ $option->id }}, {{ $index }})"
                                                         id="option-{{ $question->id }}-{{ $option->id }}">
                                                        <div class="option-letter" data-label="{{ $option->option_label }}">
                                                            @if(isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id)
                                                                <i class="ph ph-check"></i>
                                                            @else
                                                                {{ $option->option_label }}
                                                            @endif
                                                        </div>
                                                        <div class="option-text">{{ $option->option_text }}</div>
                                                        <input type="radio" 
                                                               name="question_{{ $question->id }}" 
                                                               value="{{ $option->id }}"
                                                               class="d-none"
                                                               {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="essay-container">
                                                <label class="d-flex align-items-center gap-2 mb-2 fw-semibold text-secondary" style="font-size: 0.85rem;">
                                                    <i class="ph ph-pencil-simple"></i>
                                                    Jawaban Anda:
                                                </label>
                                                <textarea id="essay-{{ $question->id }}"
                                                          class="essay-textarea"
                                                          placeholder="Tulis jawaban Anda di sini..."
                                                          oninput="onEssayInput({{ $question->id }}, {{ $index }})"
                                                          onblur="saveEssayAnswer({{ $question->id }}, {{ $index }})"
                                                >{{ isset($answeredQuestions[$question->id]) ? $answeredQuestions[$question->id]->essay_answer : '' }}</textarea>
                                                <div class="mt-2 d-flex justify-content-between align-items-center text-secondary" style="font-size: 0.75rem;">
                                                    <span><i class="ph ph-info me-1"></i>Jawaban otomatis tersimpan</span>
                                                    <span id="essay-status-{{ $question->id }}" class="text-success" style="display: none;">
                                                        <i class="ph ph-check-circle me-1"></i>Tersimpan
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Navigation Footer -->
                                    <div class="nav-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button onclick="goToQuestion({{ $index - 1 }})" 
                                                    class="btn btn-nav btn-nav-outline {{ $index === 0 ? 'invisible' : '' }}">
                                                <i class="ph ph-arrow-left"></i>
                                                <span class="d-none d-sm-inline">Sebelumnya</span>
                                            </button>

                                            <button onclick="toggleFlag({{ $index }})" id="flag-btn-{{ $index }}" class="btn-flag" title="Tandai soal ini">
                                                <i class="ph ph-flag"></i>
                                                <span class="d-none d-sm-inline">Tandai</span>
                                            </button>
                                            
                                            @if($index === $questions->count() - 1)
                                                <button onclick="confirmSubmit()" class="btn btn-nav btn-nav-success">
                                                    <span>Kumpulkan</span>
                                                    <i class="ph ph-paper-plane-tilt"></i>
                                                </button>
                                            @else
                                                <button onclick="goToQuestion({{ $index + 1 }})" class="btn btn-nav btn-nav-primary">
                                                    <span class="d-none d-sm-inline">Selanjutnya</span>
                                                    <i class="ph ph-arrow-right"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Submit Modal -->
    <div id="submit-modal" class="exam-modal-backdrop">
        <div class="exam-modal">
            <div class="exam-modal-header primary">
                <div class="exam-modal-icon">
                    <i class="ph ph-paper-plane-tilt"></i>
                </div>
                <h3 class="exam-modal-title">Kumpulkan Ujian?</h3>
            </div>
            <div class="exam-modal-body">
                <div id="submit-summary" class="submit-summary">
                    <!-- Filled by JS -->
                </div>
                <p class="text-center text-secondary mb-0">Apakah Anda yakin ingin mengumpulkan ujian?</p>
            </div>
            <div class="exam-modal-footer">
                <button onclick="closeSubmitModal()" class="btn btn-nav btn-nav-outline flex-fill">
                    <i class="ph ph-arrow-left"></i> Kembali
                </button>
                <form id="submit-form" action="{{ route('student.exams.submit', $attempt) }}" method="POST" class="flex-fill">
                    @csrf
                    <button type="submit" class="btn btn-nav btn-nav-success w-100">
                        Ya, Kumpulkan <i class="ph ph-check"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Fullscreen Modal -->
    <div id="fullscreen-modal" class="exam-modal-backdrop">
        <div class="exam-modal">
            <div class="exam-modal-header warning">
                <div class="exam-modal-icon">
                    <i class="ph ph-arrows-out"></i>
                </div>
                <h3 class="exam-modal-title">Mode Fullscreen</h3>
            </div>
            <div class="exam-modal-body">
                <div class="alert alert-warning mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ph ph-info fs-5 mt-1"></i>
                        <p class="mb-0" style="font-size: 0.9rem;">Keluar dari mode fullscreen akan dicatat sebagai pelanggaran. Maksimal {{ $attempt->exam->settings?->auto_submit_threshold ?? 5 }} pelanggaran sebelum ujian otomatis disubmit.</p>
                    </div>
                </div>
            </div>
            <div class="exam-modal-footer">
                <button onclick="enterFullscreen()" class="btn btn-nav btn-nav-primary w-100">
                    <i class="ph ph-arrows-out me-2"></i>Aktifkan Fullscreen
                </button>
            </div>
        </div>
    </div>

    <!-- No Face Warning Modal -->
    <div id="face-warning-modal" class="exam-modal-backdrop face-modal">
        <div class="exam-modal">
            <div class="exam-modal-header danger">
                <div class="exam-modal-icon">
                    <i class="ph ph-user-minus"></i>
                </div>
                <h3 class="exam-modal-title">Wajah Tidak Terdeteksi!</h3>
            </div>
            <div class="exam-modal-body">
                <p class="text-center mb-3">
                    Pastikan wajah Anda terlihat jelas di kamera. Pelanggaran akan dicatat dalam
                    <span id="face-countdown" class="fw-bold text-danger">5</span> detik.
                </p>
                <div class="alert alert-warning">
                    <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                        <li>Posisikan wajah di tengah kamera</li>
                        <li>Pastikan pencahayaan cukup</li>
                        <li>Lepas kacamata jika perlu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Multiple Faces Warning Modal -->
    <div id="multiple-faces-modal" class="exam-modal-backdrop face-modal">
        <div class="exam-modal">
            <div class="exam-modal-header danger">
                <div class="exam-modal-icon">
                    <i class="ph ph-users"></i>
                </div>
                <h3 class="exam-modal-title">Beberapa Wajah Terdeteksi!</h3>
            </div>
            <div class="exam-modal-body">
                <p class="text-center mb-3">
                    Terdeteksi <span id="faces-count" class="fw-bold text-danger">2</span> wajah di depan kamera. 
                    Ujian harus dikerjakan sendiri. Pelanggaran ini telah dicatat.
                </p>
            </div>
            <div class="exam-modal-footer">
                <button onclick="closeMultipleFacesModal()" class="btn btn-nav btn-nav-primary w-100">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- Face-api.js -->
    <script src="{{ asset('assets/proctoring/face-api.min.js') }}"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        // Configuration
        const config = {
            attemptId: {{ $attempt->id }},
            examId: {{ $attempt->exam->id }},
            totalQuestions: {{ $questions->count() }},
            remainingTime: {{ $attempt->remaining_time }},
            requireCamera: {{ $attempt->exam->settings?->webcam_enabled ? 'true' : 'false' }},
            requireFullscreen: {{ $attempt->exam->settings?->browser_lock_enabled ? 'true' : 'false' }},
            detectFace: {{ ($attempt->exam->settings?->detect_face ?? true) ? 'true' : 'false' }},
            detectMultipleFaces: {{ ($attempt->exam->settings?->detect_multiple_faces ?? true) ? 'true' : 'false' }},
            detectTabSwitch: {{ $attempt->exam->settings?->detect_tab_switch ? 'true' : 'false' }},
            detectCopyPaste: {{ $attempt->exam->settings?->detect_copy_paste ? 'true' : 'false' }},
            detectRightClick: {{ $attempt->exam->settings?->detect_right_click ? 'true' : 'false' }},
            blockKeyboardShortcuts: {{ $attempt->exam->settings?->block_keyboard_shortcuts ? 'true' : 'false' }},
            detectFullscreenExit: {{ $attempt->exam->settings?->detect_fullscreen_exit ? 'true' : 'false' }},
            snapshotInterval: {{ $attempt->exam->settings?->snapshot_interval ?? 30 }},
            maxViolations: {{ $attempt->exam->settings?->auto_submit_threshold ?? $attempt->exam->settings?->max_tab_switches ?? 5 }},
            warningThreshold: {{ $attempt->exam->settings?->warning_threshold ?? 3 }},
            csrfToken: '{{ csrf_token() }}',
            modelPath: '{{ asset("assets/proctoring/models") }}',
            endpoints: {
                saveAnswer: '{{ route("student.exams.save-answer", $attempt) }}',
                logViolation: '{{ route("student.proctoring.violation", $attempt) }}',
                uploadSnapshot: '{{ route("student.proctoring.snapshot", $attempt) }}',
                heartbeat: '{{ route("student.proctoring.heartbeat", $attempt) }}',
                autoSubmit: '{{ route("student.exams.auto-submit", $attempt) }}',
            }
        };

        // State
        let currentQuestion = 0;
        let violationCount = {{ $attempt->violation_count }};
        let answeredQuestions = new Set([
            @foreach($answeredQuestions as $questionId => $answer)
                {{ $questionId }},
            @endforeach
        ]);
        let stream = null;
        let timerInterval = null;
        let timeSyncInterval = null;
        let snapshotInterval = null;
        let heartbeatInterval = null;
        let faceDetectionInterval = null;
        let noFaceWarningTimeout = null;
        
        // Offline Queue System
        let offlineQueue = [];
        let isOnline = navigator.onLine;
        let isSyncing = false;

        // Flag / Bookmark
        let flaggedQuestions = new Set();

        // Initialize
        document.addEventListener('DOMContentLoaded', async function() {
            // Setup online/offline handlers
            initOfflineHandler();
            
            // Restore any unsaved answers from localStorage
            restoreUnsavedAnswers();
            
            initTimer();
            updateProgressBar();
            restorePreferences();
            
            // Start auto-save for essay answers
            startEssayAutoSave();
            
            // Initialize proctoring
            await initAdvancedProctoring();
            
            // Fullscreen
            initFullscreen();
        });
        
        // Offline Handler
        function initOfflineHandler() {
            window.addEventListener('online', () => {
                isOnline = true;
                hideConnectionWarning();
                syncOfflineAnswers();
            });
            
            window.addEventListener('offline', () => {
                isOnline = false;
                showConnectionWarning();
            });
            
            // Initial state
            updateConnectionIndicator(navigator.onLine);
        }
        
        function updateConnectionIndicator(online) {
            const indicator = document.getElementById('connection-indicator');
            const dot = document.getElementById('connection-dot');
            const text = document.getElementById('connection-text');
            
            if (online) {
                indicator.style.background = 'rgba(44, 168, 127, 0.2)';
                dot.style.background = '#22c55e';
                text.textContent = 'Online';
            } else {
                indicator.style.background = 'rgba(220, 38, 38, 0.2)';
                dot.style.background = '#dc2626';
                text.textContent = 'Offline';
            }
        }
        
        function showConnectionWarning() {
            updateConnectionIndicator(false);
            showWarning('⚠️ Koneksi terputus! Jawaban akan disimpan lokal dan disinkronkan saat online.');
        }
        
        function hideConnectionWarning() {
            updateConnectionIndicator(true);
            showNotification('✅ Koneksi tersambung kembali. Menyinkronkan jawaban...');
        }
        
        function showNotification(message) {
            const banner = document.getElementById('warning-banner');
            const msg = document.getElementById('warning-message');
            msg.textContent = message;
            banner.classList.add('show');
            banner.style.background = 'linear-gradient(135deg, #2ca87f 0%, #22c55e 100%)';
            setTimeout(() => {
                banner.classList.remove('show');
                banner.style.background = '';
            }, 3000);
        }
        
        async function syncOfflineAnswers() {
            if (isSyncing || offlineQueue.length === 0) return;
            
            isSyncing = true;
            
            while (offlineQueue.length > 0 && isOnline) {
                const data = offlineQueue[0];
                
                try {
                    const response = await fetch(config.endpoints.saveAnswer, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify(data.payload)
                    });
                    
                    if (response.ok) {
                        // Remove from queue and localStorage
                        offlineQueue.shift();
                        localStorage.removeItem(`answer_${config.attemptId}_${data.payload.question_id}`);
                        
                        answeredQuestions.add(data.payload.question_id);
                        updateNavButton(data.questionIndex, true);
                        updateProgressBar();
                    } else {
                        break; // Stop on server error
                    }
                } catch (err) {
                    console.error('Sync error:', err);
                    break;
                }
            }
            
            isSyncing = false;
            
            if (offlineQueue.length === 0) {
                showNotification('✅ Semua jawaban berhasil disinkronkan!');
            }
        }
        
        function restoreUnsavedAnswers() {
            // Check localStorage for any unsaved answers
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith(`answer_${config.attemptId}_`)) {
                    try {
                        const data = JSON.parse(localStorage.getItem(key));
                        if (data && data.payload) {
                            offlineQueue.push(data);
                        }
                    } catch (e) {
                        console.error('Error restoring answer:', e);
                    }
                }
            }
            
            if (offlineQueue.length > 0 && isOnline) {
                setTimeout(syncOfflineAnswers, 2000);
            }
        }

        // Toggle Sidebar (Mobile)
        function toggleSidebar() {
            document.getElementById('exam-sidebar').classList.toggle('show');
        }

        // Timer with Server Sync
        function initTimer() {
            let timeRemaining = config.remainingTime;
            const timerBox = document.getElementById('timer-box');
            const timerDisplay = document.getElementById('timer-display');
            
            function updateTimer() {
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    clearInterval(timeSyncInterval);
                    autoSubmit();
                    return;
                }
                
                const hours = Math.floor(timeRemaining / 3600);
                const minutes = Math.floor((timeRemaining % 3600) / 60);
                const seconds = timeRemaining % 60;
                
                let display = '';
                if (hours > 0) {
                    display = `${hours.toString().padStart(2, '0')}:`;
                }
                display += `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                timerDisplay.textContent = display;
                
                // Warning when less than 5 minutes
                if (timeRemaining <= 300) {
                    timerBox.classList.add('warning');
                }
                
                timeRemaining--;
            }
            
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
            
            // Sync timer with server every 60 seconds
            timeSyncInterval = setInterval(async () => {
                if (!isOnline) return;
                
                try {
                    const response = await fetch(config.endpoints.heartbeat, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify({ 
                            camera_enabled: stream !== null,
                            client_time: Math.floor(Date.now() / 1000)
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.remaining_time !== undefined) {
                        // Correct timer if drift > 5 seconds
                        const drift = Math.abs(timeRemaining - data.remaining_time);
                        if (drift > 5) {
                            console.log('Timer synced, drift was:', drift);
                            timeRemaining = data.remaining_time;
                        }
                    }
                    
                    if (data.should_submit || data.time_expired) {
                        clearInterval(timerInterval);
                        clearInterval(timeSyncInterval);
                        autoSubmit();
                    }
                } catch (e) {
                    console.error('Time sync failed:', e);
                }
            }, 60000);
        }

        // Progress Bar
        function updateProgressBar() {
            const progress = (answeredQuestions.size / config.totalQuestions) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
            document.getElementById('progress-bar-sidebar').style.width = progress + '%';
            
            const countEl = document.getElementById('answered-count');
            const percentEl = document.getElementById('answered-percent');
            const headerTextEl = document.getElementById('header-progress-text');
            if (countEl) countEl.textContent = answeredQuestions.size;
            if (percentEl) percentEl.textContent = Math.round(progress) + '%';
            if (headerTextEl) headerTextEl.textContent = answeredQuestions.size + '/' + config.totalQuestions + ' (' + Math.round(progress) + '%)';
        }

        // Question Navigation
        function goToQuestion(index) {
            if (index < 0 || index >= config.totalQuestions) return;
            
            // Hide all panels
            document.querySelectorAll('.question-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Show selected panel
            document.getElementById(`question-${index}`).classList.add('active');
            
            // Update nav buttons
            document.querySelectorAll('.q-nav-btn').forEach((btn, i) => {
                btn.classList.remove('current');
                if (i === index) btn.classList.add('current');
            });
            
            currentQuestion = index;
            
            // Scroll to top
            document.querySelector('.exam-content').scrollTop = 0;
        }

        // Select Option with Offline Support
        async function selectOption(questionId, optionId, questionIndex) {
            // Update UI immediately
            const allOptions = document.querySelectorAll(`[id^="option-${questionId}-"]`);
            allOptions.forEach(opt => {
                opt.classList.remove('selected');
                const letter = opt.querySelector('.option-letter');
                letter.innerHTML = letter.dataset.label;
            });
            
            const selected = document.getElementById(`option-${questionId}-${optionId}`);
            selected.classList.add('selected');
            selected.querySelector('.option-letter').innerHTML = '<i class="ph ph-check"></i>';
            
            const payload = { question_id: questionId, option_id: optionId };
            const queueData = { payload, questionIndex };
            
            // Save to localStorage as backup
            localStorage.setItem(`answer_${config.attemptId}_${questionId}`, JSON.stringify(queueData));
            
            // If offline, queue and return
            if (!isOnline) {
                offlineQueue.push(queueData);
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
                showNotification('📝 Jawaban disimpan offline');
                return;
            }
            
            // Save answer to server
            try {
                const response = await fetch(config.endpoints.saveAnswer, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify(payload)
                });
                
                if (response.ok) {
                    // Remove from localStorage after successful save
                    localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                    answeredQuestions.add(questionId);
                    updateNavButton(questionIndex, true);
                    updateProgressBar();
                }
            } catch (err) {
                console.error('Error saving answer:', err);
                // Queue for later retry
                offlineQueue.push(queueData);
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
                showNotification('⚠️ Jawaban disimpan lokal, akan disinkronkan...');
                setTimeout(syncOfflineAnswers, 5000);
            }
        }

        // Save Essay with Offline Support
        async function saveEssayAnswer(questionId, questionIndex) {
            const textarea = document.getElementById(`essay-${questionId}`);
            const answer = textarea.value.trim();
            
            if (!answer) return;
            
            const payload = { question_id: questionId, essay_answer: answer };
            const queueData = { payload, questionIndex };
            
            // Save to localStorage as backup
            localStorage.setItem(`answer_${config.attemptId}_${questionId}`, JSON.stringify(queueData));
            
            // If offline, queue and return
            if (!isOnline) {
                offlineQueue.push(queueData);
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
                showEssayStatus(questionId, 'offline');
                showNotification('📝 Jawaban disimpan offline');
                return;
            }
            
            try {
                const response = await fetch(config.endpoints.saveAnswer, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify(payload)
                });
                
                if (response.ok) {
                    localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                    answeredQuestions.add(questionId);
                    updateNavButton(questionIndex, true);
                    updateProgressBar();
                    showEssayStatus(questionId, 'saved');
                }
            } catch (err) {
                console.error('Error saving essay:', err);
                offlineQueue.push(queueData);
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
                showEssayStatus(questionId, 'pending');
                showNotification('⚠️ Jawaban disimpan lokal, akan disinkronkan...');
                setTimeout(syncOfflineAnswers, 5000);
            }
        }

        // Handle essay input - save to localStorage immediately
        function onEssayInput(questionId, questionIndex) {
            const textarea = document.getElementById(`essay-${questionId}`);
            const answer = textarea.value.trim();
            
            if (answer) {
                // Save to localStorage as immediate backup
                const payload = { question_id: questionId, essay_answer: answer };
                const queueData = { payload, questionIndex };
                localStorage.setItem(`answer_${config.attemptId}_${questionId}`, JSON.stringify(queueData));
                
                // Mark as unsaved (will be saved on blur or auto-save)
                showEssayStatus(questionId, 'typing');
            }
        }
        
        // Show essay save status
        function showEssayStatus(questionId, status) {
            const statusEl = document.getElementById(`essay-status-${questionId}`);
            if (!statusEl) return;
            
            statusEl.style.display = 'inline';
            
            switch(status) {
                case 'saved':
                    statusEl.className = 'text-success';
                    statusEl.innerHTML = '<i class="ph ph-check-circle me-1"></i>Tersimpan';
                    setTimeout(() => { statusEl.style.display = 'none'; }, 3000);
                    break;
                case 'typing':
                    statusEl.className = 'text-muted';
                    statusEl.innerHTML = '<i class="ph ph-pencil-simple me-1"></i>Mengetik...';
                    break;
                case 'pending':
                    statusEl.className = 'text-warning';
                    statusEl.innerHTML = '<i class="ph ph-clock me-1"></i>Menunggu...';
                    break;
                case 'offline':
                    statusEl.className = 'text-info';
                    statusEl.innerHTML = '<i class="ph ph-cloud me-1"></i>Disimpan lokal';
                    break;
            }
        }

        // Save all essay answers before submit
        async function saveAllEssayAnswers() {
            const essayTextareas = document.querySelectorAll('.essay-textarea');
            const savePromises = [];
            
            essayTextareas.forEach((textarea, index) => {
                const answer = textarea.value.trim();
                if (answer) {
                    // Extract question ID from textarea id (format: essay-{questionId})
                    const questionId = parseInt(textarea.id.replace('essay-', ''));
                    if (questionId) {
                        const payload = { question_id: questionId, essay_answer: answer };
                        
                        // Save to server
                        const savePromise = fetch(config.endpoints.saveAnswer, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken,
                            },
                            body: JSON.stringify(payload)
                        }).then(response => {
                            if (response.ok) {
                                answeredQuestions.add(questionId);
                                localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                            }
                        }).catch(err => {
                            console.error('Error saving essay on submit:', err);
                            // Save to localStorage as backup
                            localStorage.setItem(`answer_${config.attemptId}_${questionId}`, JSON.stringify({ payload, questionIndex: index }));
                        });
                        
                        savePromises.push(savePromise);
                    }
                }
            });
            
            // Wait for all saves to complete (with timeout)
            if (savePromises.length > 0) {
                try {
                    await Promise.race([
                        Promise.all(savePromises),
                        new Promise((_, reject) => setTimeout(() => reject('timeout'), 5000))
                    ]);
                } catch (err) {
                    console.warn('Some essay answers may not have saved:', err);
                }
            }
        }

        // Auto-save essay every 10 seconds
        let essayAutoSaveInterval;
        function startEssayAutoSave() {
            essayAutoSaveInterval = setInterval(() => {
                const essayTextareas = document.querySelectorAll('.essay-textarea');
                essayTextareas.forEach((textarea) => {
                    const answer = textarea.value.trim();
                    if (answer) {
                        const questionId = parseInt(textarea.id.replace('essay-', ''));
                        const questionIndex = Array.from(document.querySelectorAll('.essay-textarea')).indexOf(textarea);
                        
                        // Check if content has changed from last saved
                        const lastSaved = textarea.dataset.lastSaved || '';
                        if (answer !== lastSaved) {
                            saveEssayAnswer(questionId, questionIndex);
                            textarea.dataset.lastSaved = answer;
                        }
                    }
                });
            }, 10000); // Every 10 seconds
        }

        function updateNavButton(index, answered) {
            const btn = document.getElementById(`nav-btn-${index}`);
            if (answered) {
                btn.classList.add('answered');
            }
        }

        // ── Flag / Bookmark ─────────────────────────────
        function toggleFlag(index) {
            if (flaggedQuestions.has(index)) {
                flaggedQuestions.delete(index);
            } else {
                flaggedQuestions.add(index);
            }
            const navBtn = document.getElementById(`nav-btn-${index}`);
            const flagBtn = document.getElementById(`flag-btn-${index}`);
            if (navBtn) navBtn.classList.toggle('flagged', flaggedQuestions.has(index));
            if (flagBtn) flagBtn.classList.toggle('active', flaggedQuestions.has(index));
            // Save to localStorage
            localStorage.setItem(`flags_${config.attemptId}`, JSON.stringify([...flaggedQuestions]));
        }

        // ── Dark Mode ───────────────────────────────────
        function toggleDarkMode() {
            const isDark = document.body.classList.toggle('dark-mode');
            const btn = document.getElementById('dark-mode-btn');
            if (btn) {
                btn.innerHTML = isDark ? '<i class="ph ph-sun"></i>' : '<i class="ph ph-moon"></i>';
                btn.classList.toggle('active', isDark);
            }
            localStorage.setItem('exam_dark_mode', isDark ? '1' : '0');
        }

        // ── Font Size ───────────────────────────────────
        function setFontSize(size) {
            document.body.classList.remove('fs-small', 'fs-large');
            if (size !== 'normal') document.body.classList.add('fs-' + size);
            document.querySelectorAll('.exam-toolbar-btn[id^="fs-"]').forEach(b => b.classList.remove('active'));
            const activeBtn = document.getElementById('fs-' + size);
            if (activeBtn) activeBtn.classList.add('active');
            localStorage.setItem('exam_font_size', size);
        }

        // ── Restore Preferences (dark mode, font size, flags) ──
        function restorePreferences() {
            // Dark mode
            if (localStorage.getItem('exam_dark_mode') === '1') {
                document.body.classList.add('dark-mode');
                const btn = document.getElementById('dark-mode-btn');
                if (btn) { btn.innerHTML = '<i class="ph ph-sun"></i>'; btn.classList.add('active'); }
            }
            // Font size
            const fs = localStorage.getItem('exam_font_size');
            if (fs && fs !== 'normal') setFontSize(fs);
            // Flags
            try {
                const saved = JSON.parse(localStorage.getItem(`flags_${config.attemptId}`) || '[]');
                saved.forEach(i => {
                    flaggedQuestions.add(i);
                    const navBtn = document.getElementById(`nav-btn-${i}`);
                    const flagBtn = document.getElementById(`flag-btn-${i}`);
                    if (navBtn) navBtn.classList.add('flagged');
                    if (flagBtn) flagBtn.classList.add('active');
                });
            } catch(e) {}
        }

        // Submit Modal
        async function confirmSubmit() {
            // Save all unsaved essay answers before showing submit modal
            await saveAllEssayAnswers();
            
            const answered = answeredQuestions.size;
            const total = config.totalQuestions;
            const percentage = Math.round((answered / total) * 100);
            
            let alertHtml = '';
            if (answered < total) {
                alertHtml = `
                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                        <i class="ph ph-warning fs-5"></i>
                        <span>Masih ada <strong>${total - answered} soal</strong> yang belum dijawab!</span>
                    </div>
                `;
            } else {
                alertHtml = `
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                        <i class="ph ph-check-circle fs-5"></i>
                        <span>Semua soal telah dijawab. Bagus!</span>
                    </div>
                `;
            }
            
            document.getElementById('submit-summary').innerHTML = `
                <div class="summary-row">
                    <span class="text-secondary">Soal Terjawab</span>
                    <span class="summary-value">${answered}/${total}</span>
                </div>
                <div class="progress mb-3" style="height: 8px; border-radius: 10px;">
                    <div class="progress-bar bg-success" style="width: ${percentage}%; border-radius: 10px;"></div>
                </div>
                ${alertHtml}
            `;
            
            document.getElementById('submit-modal').classList.add('show');
        }

        function closeSubmitModal() {
            document.getElementById('submit-modal').classList.remove('show');
        }

        // Fullscreen
        function initFullscreen() {
            if (!config.requireFullscreen) return;
            
            if (!document.fullscreenElement) {
                document.getElementById('fullscreen-modal').classList.add('show');
            }
            
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement && config.requireFullscreen) {
                    document.getElementById('fullscreen-modal').classList.add('show');
                    if (config.detectFullscreenExit) {
                        logViolation('fullscreen_exit', 'User exited fullscreen mode');
                    }
                } else {
                    document.getElementById('fullscreen-modal').classList.remove('show');
                }
            });
        }

        function enterFullscreen() {
            document.documentElement.requestFullscreen().then(() => {
                document.getElementById('fullscreen-modal').classList.remove('show');
            }).catch(err => console.error('Fullscreen error:', err));
        }

        // Proctoring
        async function initAdvancedProctoring() {
            if (!config.requireCamera) {
                initBasicProctoring();
                return;
            }

            try {
                // Load face-api models
                await faceapi.nets.tinyFaceDetector.loadFromUri(config.modelPath);
                
                // Init camera
                await initCamera();
                
                // Start face detection
                if (config.detectFace) startFaceDetection();
                
                // Start snapshots
                startSnapshotCapture();
                
                // Start heartbeat
                startHeartbeat();

            } catch (error) {
                console.error('[Proctoring] Error:', error);
            }

            initBasicProctoring();
        }

        function initBasicProctoring() {
            // Tab visibility
            if (config.detectTabSwitch) {
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) logViolation('tab_switch', 'User switched to another tab');
                });

                // Window blur
                window.addEventListener('blur', () => logViolation('window_blur', 'Window lost focus'));
            }

            // Prevent copy/paste
            if (config.detectCopyPaste) {
                ['copy', 'cut', 'paste'].forEach(event => {
                    document.addEventListener(event, e => {
                        e.preventDefault();
                        logViolation('copy_paste', `${event} action detected`);
                    });
                });
            }
            
            // Prevent right click
            if (config.detectRightClick) {
                document.addEventListener('contextmenu', e => {
                    e.preventDefault();
                    logViolation('right_click', 'Right click detected');
                });
            }
            
            // Prevent keyboard shortcuts
            if (config.blockKeyboardShortcuts) {
                document.addEventListener('keydown', preventKeyboardShortcuts);
            }
        }

        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 320, height: 240, facingMode: 'user' },
                    audio: false 
                });
                
                const video = document.getElementById('camera-preview');
                video.srcObject = stream;
                
                await new Promise(resolve => {
                    video.onloadedmetadata = () => { video.play(); resolve(); };
                });
                
                document.getElementById('camera-placeholder').style.display = 'none';
                document.getElementById('camera-status').classList.remove('inactive');
                document.getElementById('camera-status').classList.add('active');
                
            } catch (error) {
                console.error('[Camera] Error:', error);
                logViolation('camera_disabled', 'Camera access denied');
            }
        }

        function startFaceDetection() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('face-canvas');
            if (!video || !canvas) return;

            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            const ctx = canvas.getContext('2d');
            
            let consecutiveNoFace = 0;

            faceDetectionInterval = setInterval(async () => {
                if (!video.videoWidth) return;

                try {
                    const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 }));
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    if (detections.length === 0) {
                        consecutiveNoFace++;
                        if (consecutiveNoFace >= 3 && !noFaceWarningTimeout) showNoFaceWarning();
                    } else if (detections.length === 1) {
                        consecutiveNoFace = 0;
                        hideNoFaceWarning();
                        const box = detections[0].box;
                        ctx.strokeStyle = '#22c55e';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(box.x, box.y, box.width, box.height);
                    } else if (detections.length > 1 && config.detectMultipleFaces) {
                        consecutiveNoFace = 0;
                        logViolation('multiple_faces', `${detections.length} faces detected`);
                        showMultipleFacesWarning(detections.length);
                        detections.forEach(d => {
                            ctx.strokeStyle = '#ef4444';
                            ctx.lineWidth = 2;
                            ctx.strokeRect(d.box.x, d.box.y, d.box.width, d.box.height);
                        });
                    }
                } catch (e) { console.error('[FaceDetection]', e); }
            }, 2000);
        }

        function showNoFaceWarning() {
            const modal = document.getElementById('face-warning-modal');
            const countdown = document.getElementById('face-countdown');
            modal.classList.add('show');
            let seconds = 5;
            countdown.textContent = seconds;

            noFaceWarningTimeout = setInterval(() => {
                seconds--;
                countdown.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(noFaceWarningTimeout);
                    noFaceWarningTimeout = null;
                    modal.classList.remove('show');
                    logViolation('no_face_detected', 'Face not detected');
                    captureAndUploadSnapshot('no_face_detected', 'No face detected');
                }
            }, 1000);
        }

        function hideNoFaceWarning() {
            document.getElementById('face-warning-modal').classList.remove('show');
            if (noFaceWarningTimeout) { clearInterval(noFaceWarningTimeout); noFaceWarningTimeout = null; }
        }

        function showMultipleFacesWarning(count) {
            document.getElementById('faces-count').textContent = count;
            document.getElementById('multiple-faces-modal').classList.add('show');
        }

        function closeMultipleFacesModal() {
            document.getElementById('multiple-faces-modal').classList.remove('show');
        }

        function startSnapshotCapture() {
            if (config.snapshotInterval <= 0) return;
            snapshotInterval = setInterval(() => captureAndUploadSnapshot(), config.snapshotInterval * 1000);
        }

        function captureAndUploadSnapshot(violationType = null, description = null) {
            const video = document.getElementById('camera-preview');
            if (!video || !stream) return;

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Timestamp
            ctx.fillStyle = 'rgba(0,0,0,0.6)';
            ctx.fillRect(0, canvas.height - 20, canvas.width, 20);
            ctx.fillStyle = '#fff';
            ctx.font = '11px Arial';
            ctx.fillText(new Date().toLocaleString('id-ID'), 4, canvas.height - 6);
            
            fetch(config.endpoints.uploadSnapshot, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify({ snapshot: canvas.toDataURL('image/jpeg', 0.7), violation_type: violationType, description: description })
            }).catch(e => console.error('[Snapshot]', e));
        }

        function startHeartbeat() {
            heartbeatInterval = setInterval(async () => {
                try {
                    const response = await fetch(config.endpoints.heartbeat, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                        body: JSON.stringify({ camera_enabled: stream !== null })
                    });
                    const data = await response.json();
                    if (data.should_submit) autoSubmit();
                } catch (e) { console.error('[Heartbeat]', e); }
            }, 30000);
        }

        async function logViolation(type, description) {
            violationCount++;
            updateViolationCounter();
            showWarning(description);
            
            if (stream) captureAndUploadSnapshot(type, description);
            
            try {
                const response = await fetch(config.endpoints.logViolation, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                    body: JSON.stringify({ violation_type: type, description: description })
                });
                const data = await response.json();
                if (data.should_auto_submit) autoSubmit();
            } catch (e) { console.error('[Violation]', e); }
        }

        function updateViolationCounter() {
            const badge = document.getElementById('violation-badge');
            const count = document.getElementById('violation-count');
            badge.classList.add('show');
            count.textContent = violationCount;
        }

        function showWarning(message) {
            const banner = document.getElementById('warning-banner');
            const msg = document.getElementById('warning-message');
            msg.textContent = `⚠️ ${message} (${violationCount}/${config.maxViolations})`;
            banner.classList.add('show');
            setTimeout(() => banner.classList.remove('show'), 5000);
        }

        function preventKeyboardShortcuts(e) {
            const blocked = ['c', 'v', 'x', 'a', 'p', 's', 'f', 'u'];
            if ((e.ctrlKey || e.metaKey) && blocked.includes(e.key.toLowerCase())) {
                e.preventDefault();
                logViolation('keyboard_shortcut', `Blocked: Ctrl+${e.key}`);
            }
            if (e.key === 'F12' || e.key === 'F5') {
                e.preventDefault();
                logViolation('keyboard_shortcut', `Blocked ${e.key}`);
            }
        }

        async function autoSubmit() {
            // Try to sync any remaining offline answers before submit
            if (offlineQueue.length > 0 && isOnline) {
                await syncOfflineAnswers();
            }
            
            try {
                const response = await fetch(config.endpoints.autoSubmit, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }
                });
                const data = await response.json();
                if (data.redirect) window.location.href = data.redirect;
            } catch (e) {
                console.error('[AutoSubmit]', e);
                document.getElementById('submit-form').submit();
            }
        }

        // Cleanup
        window.addEventListener('beforeunload', function(e) {
            // Stop camera stream
            if (stream) stream.getTracks().forEach(track => track.stop());
            
            // Clear all intervals including timer sync and essay auto-save
            [snapshotInterval, timerInterval, timeSyncInterval, heartbeatInterval, faceDetectionInterval, noFaceWarningTimeout, essayAutoSaveInterval].forEach(i => { 
                if (i) clearInterval(i); 
            });
            
            // Warning if there are unsaved answers
            if (offlineQueue.length > 0) {
                e.preventDefault();
                e.returnValue = 'Ada jawaban yang belum tersimpan. Yakin ingin keluar?';
                return e.returnValue;
            }
            
            e.preventDefault();
            e.returnValue = 'Ujian sedang berlangsung. Yakin ingin keluar?';
            return e.returnValue;
        });
        
        // Clean up localStorage on successful submit
        document.getElementById('submit-form').addEventListener('submit', function() {
            // Clear all answers for this attempt from localStorage
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key && key.startsWith(`answer_${config.attemptId}_`)) {
                    localStorage.removeItem(key);
                }
            }
        });
    </script>
</body>
</html>
