<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $attempt->exam->title }} - {{ config('app.name', 'ZAFProctor') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Custom Exam Styles -->
    <style>
        :root {
            --pc-sidebar-width: 280px;
            --pc-header-height: 70px;
            --exam-primary: #0f766e;
            --exam-primary-2: #1d4ed8;
            --exam-success: #16a34a;
            --exam-warning: #d97706;
            --exam-danger: #dc2626;
            --exam-dark: #0b1f34;
            --exam-dark-2: #123658;
            --exam-light: #f3f7fb;
            --exam-soft: rgba(15, 118, 110, 0.14);
        }

        * {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
        }

        @if($attempt->exam->settings?->detect_copy_paste)
        /* Anti-cheat: block all text selection (override UA defaults) */
        *, *::before, *::after {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }
        @endif

        body {
            background:
                radial-gradient(120% 90% at 10% 0%, rgba(29, 78, 216, 0.12) 0%, transparent 56%),
                radial-gradient(90% 80% at 100% 100%, rgba(15, 118, 110, 0.12) 0%, transparent 52%),
                linear-gradient(135deg, #f4f7fb 0%, #e6edf5 100%);
            min-height: 100vh;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: linear-gradient(rgba(15, 23, 42, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
            mask-image: radial-gradient(circle at 50% 32%, rgba(0, 0, 0, 0.8) 26%, transparent 86%);
            z-index: 0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--exam-primary); border-radius: 10px; opacity: 0.5; }

        /* Header */
        .exam-header {
            background: linear-gradient(130deg, var(--exam-dark) 0%, var(--exam-dark-2) 68%, #091626 100%);
            height: var(--pc-header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.28);
        }

        .exam-header h1,
        .progress-value,
        .exam-modal-title {
            font-family: 'Sora', 'Segoe UI', sans-serif;
            letter-spacing: -0.02em;
        }

        /* Sidebar */
        .exam-sidebar {
            position: fixed;
            left: 0;
            top: var(--pc-header-height);
            width: var(--pc-sidebar-width);
            height: calc(100vh - var(--pc-header-height));
            background: linear-gradient(180deg, #081a2c 0%, #0d2540 58%, #061322 100%);
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

        /* Focus-Lock Overlay */
        #focus-lock-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            cursor: pointer;
        }
        #focus-lock-overlay.active { display: flex; }
        .focus-lock-card {
            background: #1e293b;
            border: 2px solid #ef4444;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .focus-lock-card .icon-wrap {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.15);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .focus-lock-card h3 { color: #f87171; font-size: 1.25rem; margin-bottom: 8px; }
        .focus-lock-card p { color: #94a3b8; font-size: 0.9rem; margin-bottom: 20px; }
        .focus-lock-card .btn-return {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff; border: none; border-radius: 10px;
            padding: 12px 32px; font-weight: 600; font-size: 1rem;
            cursor: pointer; transition: transform 0.2s;
        }
        .focus-lock-card .btn-return:hover { transform: scale(1.05); }

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
            background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-2) 100%);
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
            border: 1px solid rgba(148, 163, 184, 0.28);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .question-header {
            background: linear-gradient(135deg, var(--exam-dark) 0%, #173a5e 100%);
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
            background: rgba(56, 189, 248, 0.18);
            color: #bae6fd;
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
            align-items: center;
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
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.06) 0%, rgba(29, 78, 216, 0.06) 100%);
            transform: translateX(4px);
        }
        .option-item.selected {
            border-color: var(--exam-primary);
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.12) 0%, rgba(29, 78, 216, 0.1) 100%);
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
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            user-select: text !important;
            -webkit-touch-callout: default !important;
        }
        .essay-textarea:focus {
            outline: none;
            border-color: var(--exam-primary);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
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
            background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-2) 100%);
            border: none;
            color: #fff;
        }
        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 118, 110, 0.3);
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
        .exam-modal-header.primary { background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-2) 100%); }
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
        body.dark-mode .question-body { color: #f0f0f5; }
        body.dark-mode .question-text { color: #f0f0f5; }
        body.dark-mode .option-item { background: #252536; border-color: #3a3a50; color: #e8e8f0; }
        body.dark-mode .option-item:hover { background: #2d2d44; border-color: #5b5b7a; }
        body.dark-mode .option-item.selected { background: rgba(15, 118, 110, 0.22); border-color: var(--exam-primary); }
        body.dark-mode .option-text { color: #e8e8f0; }
        body.dark-mode .option-letter { border-color: #4a4a60; color: #b0b0c8; background: rgba(255,255,255,0.04); }
        body.dark-mode .option-item.selected .option-letter { background: var(--exam-primary); border-color: var(--exam-primary); color: #fff; }
        body.dark-mode .essay-textarea { background: #252536; border-color: #3a3a50; color: #f0f0f5; }
        body.dark-mode .essay-textarea::placeholder { color: #6b6b80; }
        body.dark-mode .essay-textarea:focus { border-color: var(--exam-primary); box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.18); }
        body.dark-mode .nav-footer { background: #1a1a2a; border-color: #2a2a3d; }
        body.dark-mode .btn-nav-outline { background: #252536; border-color: #3a3a50; color: #d0d0e0; }
        body.dark-mode .btn-nav-outline:hover { background: #2d2d44; border-color: #505068; color: #f0f0f5; }
        body.dark-mode .exam-content { background: #141421; }
        body.dark-mode .btn-flag { border-color: rgba(245,158,11,0.25); color: #8a8a9e; }
        body.dark-mode .btn-flag:hover, body.dark-mode .btn-flag.active { background: rgba(245,158,11,0.12); color: #f59e0b; border-color: #f59e0b; }
        body.dark-mode .question-body img { border-radius: 8px; border: 1px solid #3a3a50; }
        body.dark-mode .exam-modal { background: #1e1e2e; }
        body.dark-mode .exam-modal-body { color: #d0d0e0; }
        body.dark-mode .exam-modal-body p { color: #d0d0e0; }
        body.dark-mode .submit-summary { background: #252536; }
        body.dark-mode .summary-value { color: #f0f0f5; }
        body.dark-mode .exam-modal-footer .btn-nav-outline { background: #252536; border-color: #3a3a50; color: #d0d0e0; }
        body.dark-mode .essay-container label.text-secondary { color: #9090a8 !important; }
        body.dark-mode .essay-container .text-secondary { color: #808099 !important; }
        body.dark-mode .exam-modal-body .alert { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1); color: #d0d0e0; }
        body.dark-mode .exam-modal-body .alert-warning { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.2); color: #fbbf24; }
        body.dark-mode .exam-modal-body .alert-success { background: rgba(34,197,94,0.1); border-color: rgba(34,197,94,0.2); color: #4ade80; }
        body.dark-mode .exam-modal-body .text-secondary { color: #9090a8 !important; }
        body.dark-mode .submit-summary .text-secondary { color: #9090a8 !important; }
        body.dark-mode .submit-summary .progress { background: rgba(255,255,255,0.1); }
        body.dark-mode { background: #141421; }

        /* ── Font Size (4 levels with significant differences) ── */
        /* Small: ~14px question, ~13px option */
        body.fs-small .question-text { font-size: 0.875rem; line-height: 1.7; }
        body.fs-small .option-text  { font-size: 0.825rem; line-height: 1.5; }
        body.fs-small .essay-textarea { font-size: 0.875rem; line-height: 1.6; }
        body.fs-small .option-letter { width: 32px; height: 32px; font-size: 0.8rem; }
        body.fs-small .question-number { width: 36px; height: 36px; font-size: 1rem; }

        /* Normal: default (1.15rem question, 1rem option) — no overrides needed */

        /* Large: ~22px question, ~20px option */
        body.fs-large .question-text { font-size: 1.375rem; line-height: 1.9; }
        body.fs-large .option-text  { font-size: 1.25rem; line-height: 1.7; }
        body.fs-large .essay-textarea { font-size: 1.3rem; line-height: 1.8; min-height: 240px; }
        body.fs-large .option-item  { padding: 18px 22px; margin-bottom: 14px; }
        body.fs-large .option-letter { width: 42px; height: 42px; font-size: 1rem; }
        body.fs-large .question-number { width: 46px; height: 46px; font-size: 1.2rem; }
        body.fs-large .question-body { padding: 32px; }

        /* Extra Large: ~28px question, ~24px option — for severe visual impairment */
        body.fs-xlarge .question-text { font-size: 1.75rem; line-height: 2; }
        body.fs-xlarge .option-text  { font-size: 1.5rem; line-height: 1.8; }
        body.fs-xlarge .essay-textarea { font-size: 1.6rem; line-height: 1.9; min-height: 280px; }
        body.fs-xlarge .option-item  { padding: 20px 24px; margin-bottom: 16px; border-radius: 16px; }
        body.fs-xlarge .option-letter { width: 48px; height: 48px; font-size: 1.15rem; border-radius: 12px; }
        body.fs-xlarge .question-number { width: 52px; height: 52px; font-size: 1.35rem; }
        body.fs-xlarge .question-body { padding: 36px; }
        body.fs-xlarge .question-header { padding: 24px 28px; }
        body.fs-xlarge .question-points { font-size: 1rem; padding: 8px 14px; }

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
            display: none;
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
<body
    @if($attempt->exam->settings?->detect_right_click) oncontextmenu="return false;" @endif
    @if($attempt->exam->settings?->detect_copy_paste)
    oncopy="return false;"
    onpaste="return false;"
    oncut="return false;"
    onselectstart="if(event.target.tagName!=='TEXTAREA')return false;"
    ondragstart="return false;"
    @endif
>

    <!-- Focus-Lock Overlay -->
    <div id="focus-lock-overlay" onclick="dismissFocusLock()">
        <div class="focus-lock-card" onclick="event.stopPropagation(); dismissFocusLock();">
            <div class="icon-wrap">
                <i class="ph ph-warning-circle" style="font-size: 2.5rem; color: #ef4444;"></i>
            </div>
            <h3>Peringatan!</h3>
            <p>Anda meninggalkan halaman ujian. Tindakan ini tercatat sebagai pelanggaran. Klik tombol di bawah untuk kembali ke ujian.</p>
            <button class="btn-return">Kembali ke Ujian</button>
        </div>
    </div>

    <!-- Header -->
    <header class="exam-header">
        <div class="container-fluid h-100">
            <div class="d-flex align-items-center justify-content-between h-100 px-3">
                <!-- Left: Exam Info -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-white d-lg-none p-0 me-2" onclick="toggleSidebar()">
                        <i class="ph ph-list fs-4"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--exam-primary) 0%, var(--exam-primary-2) 100%);">
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
                        <video id="camera-preview" autoplay muted playsinline disablepictureinpicture controlslist="nodownload noplaybackrate nofullscreen"></video>
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

                    <!-- Toolbar: Font Size (4 levels) -->
                    <div class="toolbar-group d-none d-md-flex">
                        <button onclick="setFontSize('small')" class="exam-toolbar-btn" title="Font kecil" id="fs-small" style="font-size:0.65rem;">A</button>
                        <button onclick="setFontSize('normal')" class="exam-toolbar-btn active" title="Font normal" id="fs-normal" style="font-size:0.8rem;">A</button>
                        <button onclick="setFontSize('large')" class="exam-toolbar-btn" title="Font besar" id="fs-large" style="font-size:0.95rem;">A</button>
                        <button onclick="setFontSize('xlarge')" class="exam-toolbar-btn" title="Font sangat besar" id="fs-xlarge" style="font-size:1.1rem; font-weight:700;">A</button>
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
                                        
                                        @if($question->question_image_url)
                                            <div class="mb-4">
                                                <img src="{{ $question->question_image_url }}" 
                                                     alt="Gambar Soal" 
                                                     class="img-fluid rounded-3" style="max-width: 500px;"
                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'alert alert-warning py-2 px-3 small\'><i class=\'ph ph-image-broken me-1\'></i>Gambar tidak dapat dimuat</div>';">
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
                        @php
                            $maxViolations = $attempt->exam->settings?->auto_submit_threshold ?? $attempt->exam->settings?->max_tab_switches ?? 5;
                        @endphp
                        <p class="mb-0" style="font-size: 0.9rem;">
                            Keluar dari mode fullscreen akan dicatat sebagai pelanggaran.
                            @if($maxViolations > 0)
                                Maksimal {{ $maxViolations }} pelanggaran sebelum ujian otomatis disubmit.
                            @else
                                Pelanggaran tetap dicatat tanpa auto-submit berdasarkan jumlah pelanggaran.
                            @endif
                        </p>
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
        // Configuration - frozen to prevent tampering
        const config = Object.freeze({
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
            csrfToken: '{{ csrf_token() }}',
            modelPath: '{{ asset("assets/proctoring/models") }}',
            endpoints: Object.freeze({
                saveAnswer: '{{ route("student.exams.save-answer", $attempt) }}',
                logViolation: '{{ route("student.proctoring.violation", $attempt) }}',
                uploadSnapshot: '{{ route("student.proctoring.snapshot", $attempt) }}',
                heartbeat: '{{ route("student.proctoring.heartbeat", $attempt) }}',
                syncTime: '{{ route("student.exams.sync-time", $attempt) }}',
                autoSubmit: '{{ route("student.exams.auto-submit", $attempt) }}',
            })
        });

        // State
        let currentQuestion = 0;
        let violationCount = {{ $attempt->violation_count }};
        let maxViolations = Number.isFinite(Number(config.maxViolations)) ? Number(config.maxViolations) : 0;
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
        let isSubmitting = false;
        let lastMultipleFacesTime = 0;
        let lastViolationSnapshotTime = 0;

        // Flag / Bookmark
        let flaggedQuestions = new Set();

        // ══════════════════════════════════════════════════════
        // IMMEDIATE ANTI-CHEAT: runs the MOMENT script is parsed
        // (before DOMContentLoaded, before any async operation)
        // ══════════════════════════════════════════════════════
        (function() {
            // ═══════════════════════════════════════════════════
            // 0. OVERRIDE CLIPBOARD & EXEC COMMAND APIs
            //    Prevents programmatic copy/paste via JS console
            // ═══════════════════════════════════════════════════
            if (config.detectCopyPaste) {
                // Override navigator.clipboard API
                try {
                    Object.defineProperty(navigator, 'clipboard', {
                        get: function() {
                            return {
                                readText: function() { logViolation('copy_paste', 'Clipboard API readText blocked'); return Promise.reject('blocked'); },
                                writeText: function() { logViolation('copy_paste', 'Clipboard API writeText blocked'); return Promise.reject('blocked'); },
                                read: function() { logViolation('copy_paste', 'Clipboard API read blocked'); return Promise.reject('blocked'); },
                                write: function() { logViolation('copy_paste', 'Clipboard API write blocked'); return Promise.reject('blocked'); },
                            };
                        },
                        configurable: false
                    });
                } catch(x) {}

                // Override document.execCommand for copy/cut/paste
                var _origExecCommand = document.execCommand.bind(document);
                document.execCommand = function(cmd) {
                    var c = cmd.toLowerCase();
                    if (c === 'copy' || c === 'cut' || c === 'paste' || c === 'selectall') {
                        logViolation('copy_paste', 'execCommand ' + cmd + ' blocked');
                        return false;
                    }
                    return _origExecCommand.apply(document, arguments);
                };

                // Override Selection API to prevent getSelection-based copying
                var _origGetSelection = window.getSelection.bind(window);
                window.getSelection = function() {
                    var sel = _origGetSelection();
                    // Allow selection inside essay-textarea, block everywhere else
                    var active = document.activeElement;
                    if (active && active.classList && active.classList.contains('essay-textarea')) {
                        return sel;
                    }
                    try {
                        // Return a wrapper that blocks toString
                        return {
                            toString: function() { return ''; },
                            anchorNode: sel.anchorNode,
                            anchorOffset: sel.anchorOffset,
                            focusNode: sel.focusNode,
                            focusOffset: sel.focusOffset,
                            isCollapsed: sel.isCollapsed,
                            rangeCount: sel.rangeCount,
                            type: sel.type,
                            removeAllRanges: function() { sel.removeAllRanges(); },
                            getRangeAt: function(i) { return sel.getRangeAt(i); },
                            collapse: function() { sel.collapse.apply(sel, arguments); },
                            collapseToStart: function() { sel.collapseToStart(); },
                            collapseToEnd: function() { sel.collapseToEnd(); },
                            extend: function() { sel.extend.apply(sel, arguments); },
                            addRange: function() { sel.addRange.apply(sel, arguments); },
                        };
                    } catch(e) { return sel; }
                };
            }

            // ═══════════════════════════════════════════════════
            // 1. COPY / CUT / PASTE (capture-phase, highest priority)
            // ═══════════════════════════════════════════════════
            if (config.detectCopyPaste) {
                ['copy', 'cut', 'paste'].forEach(function(evt) {
                    document.addEventListener(evt, function(e) {
                        if (isSubmitting) return;
                        // Allow nothing — not even inside essay
                        // (student can TYPE but not copy/paste)
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        try { if (e.clipboardData) e.clipboardData.clearData(); } catch(x) {}
                        logViolation('copy_paste', evt + ' action blocked');
                        return false;
                    }, true);
                });

                // Also listen on window level for extra coverage
                ['copy', 'cut', 'paste'].forEach(function(evt) {
                    window.addEventListener(evt, function(e) {
                        if (isSubmitting) return;
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }, true);
                });

                // Block all drag operations
                ['dragstart', 'drop', 'dragover', 'dragenter', 'dragleave'].forEach(function(evt) {
                    document.addEventListener(evt, function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        if (evt === 'dragstart' || evt === 'drop') logViolation('copy_paste', 'Drag operation blocked');
                        return false;
                    }, true);
                });

                // Block input event paste (catches some mobile/autofill paste attempts)
                document.addEventListener('input', function(e) {
                    if (isSubmitting) return;
                    if (e.inputType === 'insertFromPaste' || e.inputType === 'insertFromDrop') {
                        // For essay textareas we need to undo the paste
                        if (e.target && e.target.classList && e.target.classList.contains('essay-textarea')) {
                            // Attempt to undo the paste via execCommand (already overridden but try native)
                            try { _origExecCommand('undo'); } catch(x) {}
                        }
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        logViolation('copy_paste', 'Paste via input event blocked');
                    }
                }, true);
            }

            // ═══════════════════════════════════════════════════
            // 2. RIGHT-CLICK (capture-phase, both document & window)
            // ═══════════════════════════════════════════════════
            if (config.detectRightClick) {
                document.addEventListener('contextmenu', function(e) {
                    if (isSubmitting) return;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    logViolation('right_click', 'Right click blocked');
                    return false;
                }, true);
                window.addEventListener('contextmenu', function(e) {
                    if (isSubmitting) return;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
            }

            // ═══════════════════════════════════════════════════
            // 3. KEYBOARD SHORTCUTS (capture-phase, both document & window)
            // ═══════════════════════════════════════════════════
            if (config.blockKeyboardShortcuts || config.detectCopyPaste) {
                document.addEventListener('keydown', preventKeyboardShortcuts, true);
                window.addEventListener('keydown', preventKeyboardShortcuts, true);
                // Also block keyup for modifier keys to prevent held-key bypasses
                document.addEventListener('keyup', function(e) {
                    if (isSubmitting) return;
                    if (e.key === 'PrintScreen') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        // Clear clipboard after PrintScreen
                        if (config.detectCopyPaste) {
                            try { navigator.clipboard.writeText(''); } catch(x) {}
                        }
                        logViolation('keyboard_shortcut', 'PrintScreen blocked (keyup)');
                    }
                }, true);
            }

            // ═══════════════════════════════════════════════════
            // 4. TAB / WINDOW VISIBILITY (multiple detection layers)
            // ═══════════════════════════════════════════════════
            if (config.detectTabSwitch) {
                var lastSwitchTime = 0;

                // Layer 1: visibilitychange
                document.addEventListener('visibilitychange', function() {
                    if (isSubmitting) return;
                    var now = Date.now();
                    if (document.hidden && now - lastSwitchTime > 2000) {
                        lastSwitchTime = now;
                        // Capture snapshot BEFORE video stream potentially pauses
                        var blurSnap = (stream && canCaptureViolationSnapshot()) ? captureSnapshotBase64() : null;
                        logViolation('tab_switch', 'User switched to another tab', blurSnap);
                        showFocusLock();
                    }
                });

                // Layer 2: window blur
                window.addEventListener('blur', function() {
                    if (isSubmitting) return;
                    var now = Date.now();
                    if (now - lastSwitchTime > 2000) {
                        lastSwitchTime = now;
                        var blurSnap = (stream && canCaptureViolationSnapshot()) ? captureSnapshotBase64() : null;
                        logViolation('window_blur', 'Window lost focus', blurSnap);
                        showFocusLock();
                    }
                });

                // Layer 3: focus monitoring - periodically check if window has focus
                var _focusCheckInterval = setInterval(function() {
                    if (isSubmitting) { clearInterval(_focusCheckInterval); return; }
                    if (!document.hasFocus()) {
                        var now = Date.now();
                        if (now - lastSwitchTime > 3000) {
                            lastSwitchTime = now;
                            var blurSnap = (stream && canCaptureViolationSnapshot()) ? captureSnapshotBase64() : null;
                            logViolation('tab_switch', 'Window lost focus (periodic check)', blurSnap);
                            showFocusLock();
                        }
                    }
                }, 2000);

                // Layer 4: mouse leave window (detects when mouse exits the page area)
                document.addEventListener('mouseleave', function(e) {
                    if (isSubmitting) return;
                    // Only log if mouse is leaving to top (likely address bar / tab bar)
                    if (e.clientY <= 0) {
                        var now = Date.now();
                        if (now - lastSwitchTime > 5000) {
                            // Don't count as violation, but log for monitoring
                            // logViolation('suspicious', 'Mouse left to top of window');
                        }
                    }
                });
            }

            // ═══════════════════════════════════════════════════
            // 5. PRINT BLOCKING
            // ═══════════════════════════════════════════════════
            window.addEventListener('beforeprint', function(e) { e.preventDefault(); });
            window.addEventListener('afterprint', function() {
                logViolation('keyboard_shortcut', 'Print attempt detected');
            });
            // Override window.print
            window.print = function() {
                logViolation('keyboard_shortcut', 'window.print() blocked');
            };

            // ═══════════════════════════════════════════════════
            // 6. PICTURE-IN-PICTURE BLOCKING
            // ═══════════════════════════════════════════════════
            document.addEventListener('enterpictureinpicture', function(e) {
                e.preventDefault();
                try { document.exitPictureInPicture(); } catch(x) {}
            }, true);

            // ═══════════════════════════════════════════════════
            // 7. CONSOLE OVERRIDE (disable console methods)
            //    MUST run before DevTools detection so we can
            //    save original console.log for the trap
            // ═══════════════════════════════════════════════════
            var _origConsoleLog;
            (function disableConsole() {
                var noop = function() {};
                // Keep references for internal use
                window._origConsoleError = console.error.bind(console);
                _origConsoleLog = console.log.bind(console);
                var _origClear = console.clear.bind(console);
                var methods = ['log', 'debug', 'info', 'warn', 'dir', 'dirxml', 'table', 'trace', 'group', 'groupCollapsed', 'groupEnd', 'profile', 'profileEnd', 'time', 'timeEnd', 'timeStamp', 'count', 'assert'];
                methods.forEach(function(m) {
                    try { console[m] = noop; } catch(x) {}
                });
                // Clear console periodically
                setInterval(function() {
                    try { _origClear(); } catch(x) {}
                }, 2000);
            })();

            // ═══════════════════════════════════════════════════
            // 8. DEVTOOLS DETECTION (multiple methods)
            // ═══════════════════════════════════════════════════
            (function detectDevTools() {
                var devToolsOpen = false;
                var devToolsCheckCount = 0;

                // Method 1: debugger timing detection
                // Running debugger statement takes much longer when DevTools is open
                function checkDebuggerTiming() {
                    if (isSubmitting) return;
                    var start = performance.now();
                    (function() { debugger; })();
                    var end = performance.now();
                    if (end - start > 100) {
                        if (!devToolsOpen) {
                            devToolsOpen = true;
                            devToolsCheckCount++;
                            logViolation('devtools', 'Developer Tools detected (debugger timing)');
                        }
                    } else {
                        devToolsOpen = false;
                    }
                }

                // Method 2: Window size difference detection
                // DevTools docked panel changes inner dimensions
                var _prevWidth = window.outerWidth - window.innerWidth;
                var _prevHeight = window.outerHeight - window.innerHeight;
                function checkWindowSize() {
                    if (isSubmitting) return;
                    var widthDiff = window.outerWidth - window.innerWidth;
                    var heightDiff = window.outerHeight - window.innerHeight;
                    // Threshold of 160px typically indicates DevTools panel
                    if (widthDiff > 160 || heightDiff > 160) {
                        if (!devToolsOpen) {
                            devToolsOpen = true;
                            devToolsCheckCount++;
                            logViolation('devtools', 'Developer Tools detected (window size anomaly)');
                        }
                    }
                }

                // Method 3: Detect console.log toString trick
                // When DevTools is open, objects logged have their toString called
                var _dtElement = new Image();
                Object.defineProperty(_dtElement, 'id', {
                    get: function() {
                        if (!devToolsOpen && !isSubmitting) {
                            devToolsOpen = true;
                            devToolsCheckCount++;
                            logViolation('devtools', 'Developer Tools detected (console access)');
                        }
                    }
                });

                // Run periodic console check (uses saved original console.log)
                setInterval(function() {
                    if (isSubmitting) return;
                    devToolsOpen = false;
                    _origConsoleLog('%c', _dtElement);
                }, 3000);

                // Run window size check
                setInterval(checkWindowSize, 2000);

                // Resize event also triggers check
                window.addEventListener('resize', function() {
                    if (isSubmitting) return;
                    checkWindowSize();
                });
            })();

            // ═══════════════════════════════════════════════════
            // 9. ANTI-TAMPERING: Integrity checks
            //    Periodically verify critical functions exist
            // ═══════════════════════════════════════════════════
            (function integrityMonitor() {
                // Store references to critical functions
                var _origLogViolation = null;
                var _origPreventKeyboard = null;

                // Wait for functions to be defined, then snapshot them
                setTimeout(function() {
                    _origLogViolation = typeof logViolation === 'function' ? logViolation : null;
                    _origPreventKeyboard = typeof preventKeyboardShortcuts === 'function' ? preventKeyboardShortcuts : null;
                }, 100);

                setInterval(function() {
                    if (isSubmitting) return;

                    // Check if logViolation was redefined/nullified
                    if (_origLogViolation && typeof logViolation === 'function' && logViolation !== _origLogViolation) {
                        // Someone replaced logViolation!
                        logViolation = _origLogViolation;
                        _origLogViolation('tampering', 'Anti-cheat function was tampered with');
                    }
                    if (typeof logViolation !== 'function') {
                        // logViolation was deleted, restore it
                        if (_origLogViolation) {
                            window.logViolation = _origLogViolation;
                            _origLogViolation('tampering', 'Anti-cheat function was deleted');
                        }
                    }

                    // Check if preventKeyboardShortcuts was tampered
                    if (_origPreventKeyboard && typeof preventKeyboardShortcuts === 'function' && preventKeyboardShortcuts !== _origPreventKeyboard) {
                        preventKeyboardShortcuts = _origPreventKeyboard;
                        if (_origLogViolation) _origLogViolation('tampering', 'Keyboard blocker was tampered with');
                    }
                }, 3000);
            })();

            // ═══════════════════════════════════════════════════
            // 10. BLOCK NEW WINDOW / TAB OPENING
            // ═══════════════════════════════════════════════════
            // Override window.open
            window.open = function() {
                logViolation('keyboard_shortcut', 'window.open() blocked');
                return null;
            };

            // Block middle-click (opens new tab)
            document.addEventListener('auxclick', function(e) {
                if (isSubmitting) return;
                if (e.button === 1) { // middle click
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);

        })();

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
            
            // Initialize advanced proctoring (camera, face detection)
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

        async function parseJsonResponse(response) {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                return null;
            }

            try {
                return await response.json();
            } catch (e) {
                return null;
            }
        }

        async function handleSaveAnswerResponse(response) {
            if (response.redirected) {
                window.location.href = response.url;
                return { success: false, terminal: true };
            }

            const data = await parseJsonResponse(response);

            if (!response.ok) {
                if (data && data.attempt_submitted && data.redirect) {
                    window.location.href = data.redirect;
                    return { success: false, terminal: true };
                }

                return {
                    success: false,
                    terminal: Boolean(data && data.attempt_submitted),
                    message: (data && data.message) || 'Gagal menyimpan jawaban. Silakan coba lagi.',
                };
            }

            return { success: true, data };
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
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify(data.payload)
                    });

                    const result = await handleSaveAnswerResponse(response);
                    if (result.terminal) {
                        isSyncing = false;
                        return;
                    }

                    if (result.success) {
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
                    if (window._origConsoleError) window._origConsoleError('Sync error:', err);
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
                        if (window._origConsoleError) window._origConsoleError('Error restoring answer:', e);
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
                    const response = await fetch(config.endpoints.syncTime, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                        },
                        body: JSON.stringify({ 
                            client_time: Math.floor(Date.now() / 1000)
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.remaining_time !== undefined) {
                        // Correct timer if drift > 5 seconds
                        const drift = Math.abs(timeRemaining - data.remaining_time);
                        if (drift > 5) {
                            // Timer synced silently
                            timeRemaining = data.remaining_time;
                        }
                    }
                    
                    if (data.should_submit || data.time_expired) {
                        clearInterval(timerInterval);
                        clearInterval(timeSyncInterval);
                        autoSubmit();
                    }
                } catch (e) {
                    if (window._origConsoleError) window._origConsoleError('Time sync failed:', e);
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
            if (countEl) countEl.textContent = answeredQuestions.size;
            if (percentEl) percentEl.textContent = Math.round(progress) + '%';
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify(payload)
                });

                const result = await handleSaveAnswerResponse(response);
                if (result.terminal) {
                    return;
                }

                if (result.success) {
                    // Remove from localStorage after successful save
                    localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                    answeredQuestions.add(questionId);
                    updateNavButton(questionIndex, true);
                    updateProgressBar();
                    return;
                }

                throw new Error(result.message || 'Gagal menyimpan jawaban ke server.');
            } catch (err) {
                if (window._origConsoleError) window._origConsoleError('Error saving answer:', err);
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify(payload)
                });

                const result = await handleSaveAnswerResponse(response);
                if (result.terminal) {
                    return;
                }

                if (result.success) {
                    localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                    answeredQuestions.add(questionId);
                    updateNavButton(questionIndex, true);
                    updateProgressBar();
                    showEssayStatus(questionId, 'saved');
                    return;
                }

                throw new Error(result.message || 'Gagal menyimpan jawaban essay ke server.');
            } catch (err) {
                if (window._origConsoleError) window._origConsoleError('Error saving essay:', err);
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
                        const savePromise = (async () => {
                            const response = await fetch(config.endpoints.saveAnswer, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': config.csrfToken,
                                },
                                body: JSON.stringify(payload)
                            });

                            const result = await handleSaveAnswerResponse(response);
                            if (result.terminal) {
                                return;
                            }

                            if (result.success) {
                                answeredQuestions.add(questionId);
                                localStorage.removeItem(`answer_${config.attemptId}_${questionId}`);
                                return;
                            }

                            throw new Error(result.message || 'Gagal menyimpan jawaban sebelum submit.');
                        })().catch(err => {
                            if (window._origConsoleError) window._origConsoleError('Error saving essay on submit:', err);
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
                    if (window._origConsoleError) window._origConsoleError('Some essay answers may not have saved:', err);
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

        // ── Font Size (4 levels: small / normal / large / xlarge) ──
        function setFontSize(size) {
            document.body.classList.remove('fs-small', 'fs-large', 'fs-xlarge');
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

        // Fullscreen with aggressive re-entry
        function initFullscreen() {
            if (!config.requireFullscreen) return;
            
            if (!document.fullscreenElement) {
                document.getElementById('fullscreen-modal').classList.add('show');
            }
            
            // Listen for fullscreen exit
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement && config.requireFullscreen && !isSubmitting) {
                    document.getElementById('fullscreen-modal').classList.add('show');
                    if (config.detectFullscreenExit) {
                        logViolation('fullscreen_exit', 'User exited fullscreen mode');
                    }
                    // Aggressively try to re-enter fullscreen after a short delay
                    setTimeout(function() {
                        if (!document.fullscreenElement && !isSubmitting) {
                            try {
                                document.documentElement.requestFullscreen().then(function() {
                                    document.getElementById('fullscreen-modal').classList.remove('show');
                                    // Re-lock ALL keyboard keys after re-entering fullscreen
                                    try {
                                        if (navigator.keyboard && navigator.keyboard.lock) {
                                            navigator.keyboard.lock().catch(function() {});
                                        }
                                    } catch(x) {}
                                }).catch(function() {});
                            } catch(x) {}
                        }
                    }, 1000);
                } else {
                    document.getElementById('fullscreen-modal').classList.remove('show');
                }
            });

            // Periodic fullscreen check
            setInterval(function() {
                if (!document.fullscreenElement && config.requireFullscreen && !isSubmitting) {
                    // Keep showing the modal until they re-enter
                    var modal = document.getElementById('fullscreen-modal');
                    if (modal && !modal.classList.contains('show')) {
                        modal.classList.add('show');
                    }
                }
            }, 3000);

            // Block Escape key at the document level to prevent fullscreen exit
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && config.requireFullscreen && document.fullscreenElement) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);
        }

        function enterFullscreen() {
            document.documentElement.requestFullscreen().then(() => {
                document.getElementById('fullscreen-modal').classList.remove('show');
                // Lock ALL keyboard keys to prevent Alt+Tab, Alt+F4, Meta, etc.
                // Empty array = lock everything. Browser provides 2-sec Escape hold as safety exit.
                try {
                    if (navigator.keyboard && navigator.keyboard.lock) {
                        navigator.keyboard.lock().catch(function() {});
                    }
                } catch(x) {}
            }).catch(err => { if (window._origConsoleError) window._origConsoleError('Fullscreen error:', err); });
        }

        // Proctoring
        async function initAdvancedProctoring() {
            // Anti-cheat listeners already active from IIFE above
            if (!config.requireCamera) return;

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
                if (window._origConsoleError) window._origConsoleError('[Proctoring] Error:', error);
            }
        }

        // initBasicProctoring is now handled by the IIFE above (immediate execution)

        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 320, height: 240, facingMode: 'user' },
                    audio: false 
                });
                
                const video = document.getElementById('camera-preview');
                video.srcObject = stream;
                video.disablePictureInPicture = true;
                video.setAttribute('disablepictureinpicture', '');
                video.setAttribute('controlslist', 'nodownload noplaybackrate nofullscreen');
                
                await new Promise(resolve => {
                    video.onloadedmetadata = () => { video.play(); resolve(); };
                });
                
                document.getElementById('camera-placeholder').style.display = 'none';
                document.getElementById('camera-status').classList.remove('inactive');
                document.getElementById('camera-status').classList.add('active');
                
            } catch (error) {
                if (window._origConsoleError) window._origConsoleError('[Camera] Error:', error);
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
                if (!video.videoWidth || isSubmitting) return;

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
                        // Debounce: only log once per 10 seconds
                        const now = Date.now();
                        if (now - lastMultipleFacesTime > 10000) {
                            lastMultipleFacesTime = now;
                            logViolation('multiple_faces', `${detections.length} faces detected`);
                            showMultipleFacesWarning(detections.length);
                        }
                        detections.forEach(d => {
                            ctx.strokeStyle = '#ef4444';
                            ctx.lineWidth = 2;
                            ctx.strokeRect(d.box.x, d.box.y, d.box.width, d.box.height);
                        });
                    }
                } catch (e) { if (window._origConsoleError) window._origConsoleError('[FaceDetection]', e); }
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

        async function captureAndUploadSnapshot(violationType = null, description = null) {
            const video = document.getElementById('camera-preview');
            if (!video || !stream) return null;

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
            
            try {
                const response = await fetch(config.endpoints.uploadSnapshot, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                    body: JSON.stringify({ snapshot: canvas.toDataURL('image/jpeg', 0.7), violation_type: violationType, description: description })
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }

                const data = await response.json().catch(() => null);
                if (!response.ok) {
                    if (data && data.attempt_submitted && data.redirect) {
                        window.location.href = data.redirect;
                        return null;
                    }

                    throw new Error((data && data.message) || 'Gagal upload snapshot.');
                }

                return data;
            } catch (e) {
                if (window._origConsoleError) window._origConsoleError('[Snapshot]', e);
                return null;
            }
        }

        function startHeartbeat() {
            heartbeatInterval = setInterval(async () => {
                try {
                    const response = await fetch(config.endpoints.heartbeat, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                        body: JSON.stringify({ 
                            camera_enabled: stream !== null,
                            face_detected: !!(stream && faceDetectionInterval)
                        })
                    });
                    const data = await response.json();
                    if (data.should_submit) autoSubmit();
                } catch (e) { if (window._origConsoleError) window._origConsoleError('[Heartbeat]', e); }
            }, 30000);
        }

        function canCaptureViolationSnapshot() {
            const now = Date.now();

            // Keep violation snapshots sparse so heavy uploads do not saturate the endpoint.
            if (now - lastViolationSnapshotTime < 5000) {
                return false;
            }

            lastViolationSnapshotTime = now;
            return true;
        }

        /**
         * Capture a base64 snapshot from the webcam video element (no upload).
         */
        function captureSnapshotBase64() {
            const video = document.getElementById('camera-preview');
            if (!video || !stream) return null;

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 320;
            canvas.height = video.videoHeight || 240;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Timestamp overlay
            ctx.fillStyle = 'rgba(0,0,0,0.6)';
            ctx.fillRect(0, canvas.height - 20, canvas.width, 20);
            ctx.fillStyle = '#fff';
            ctx.font = '11px Arial';
            ctx.fillText(new Date().toLocaleString('id-ID'), 4, canvas.height - 6);

            return canvas.toDataURL('image/jpeg', 0.7);
        }

        function showFocusLock() {
            var overlay = document.getElementById('focus-lock-overlay');
            if (overlay) overlay.classList.add('active');
        }

        function dismissFocusLock() {
            var overlay = document.getElementById('focus-lock-overlay');
            if (overlay) overlay.classList.remove('active');
            // Re-request fullscreen if configured
            if (config.requireFullscreen && !document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(function() {
                    // Re-lock ALL keyboard keys after re-entering fullscreen
                    try {
                        if (navigator.keyboard && navigator.keyboard.lock) {
                            navigator.keyboard.lock().catch(function() {});
                        }
                    } catch(x) {}
                }).catch(function() {});
            }
            // Refocus the window
            window.focus();
        }

        // Auto-dismiss overlay when window regains focus
        window.addEventListener('focus', function() {
            var overlay = document.getElementById('focus-lock-overlay');
            if (overlay && overlay.classList.contains('active')) {
                dismissFocusLock();
            }
        });

        async function persistViolation(type, description, snapshot = null) {
            try {
                const payload = { violation_type: type, description: description };
                if (snapshot) {
                    payload.snapshot = snapshot;
                }
                const response = await fetch(config.endpoints.logViolation, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify(payload)
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }

                const data = await response.json().catch(() => null);
                if (!response.ok) {
                    if (data && data.attempt_submitted && data.redirect) {
                        window.location.href = data.redirect;
                        return null;
                    }

                    throw new Error((data && data.message) || 'Gagal mencatat pelanggaran.');
                }

                return data;
            } catch (e) {
                if (window._origConsoleError) window._origConsoleError('[Violation]', e);
                return null;
            }
        }

        async function logViolation(type, description, preSnap = null) {
            if (isSubmitting) return;
            
            violationCount++;
            updateViolationCounter();
            showWarning(description);

            // Use pre-captured snapshot if provided, otherwise capture now.
            let violationSnapshot = preSnap;
            if (!violationSnapshot && stream && canCaptureViolationSnapshot()) {
                violationSnapshot = captureSnapshotBase64();
            }

            // Violation count must always come from the dedicated log endpoint.
            const data = await persistViolation(type, description, violationSnapshot);

            // Also upload snapshot to the periodic snapshot endpoint for camera coverage.
            if (stream && violationSnapshot) {
                captureAndUploadSnapshot(null, `Violation snapshot: ${type}`);
            }

            // Sync counters from server to avoid client/server drift.
            if (data && Number.isFinite(Number(data.violation_count))) {
                violationCount = Number(data.violation_count);
                updateViolationCounter();
            }
            if (data && Number.isFinite(Number(data.max_violations))) {
                maxViolations = Number(data.max_violations);
            }

            if (data && data.should_auto_submit) {
                autoSubmit();
                return;
            }
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
            const violationLabel = maxViolations > 0
                ? `${violationCount}/${maxViolations}`
                : `${violationCount}/tanpa batas`;
            msg.textContent = `⚠️ ${message} (${violationLabel})`;
            banner.classList.add('show');
            setTimeout(() => banner.classList.remove('show'), 5000);
        }

        function preventKeyboardShortcuts(e) {
            if (isSubmitting) return;

            // ── Escape key (exits fullscreen) ────────────
            if (e.key === 'Escape') {
                if (config.requireFullscreen && document.fullscreenElement) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    // Don't log as violation, just block the key
                    return false;
                }
            }
            
            // ── Function keys ────────────────────────────
            const blockedFnKeys = ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12', 'PrintScreen'];
            if (blockedFnKeys.includes(e.key)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                logViolation('keyboard_shortcut', `Blocked ${e.key}`);
                return false;
            }

            // ── Ctrl / Meta + key ────────────────────────
            if (e.ctrlKey || e.metaKey) {
                const key = e.key.toLowerCase();

                // Ctrl+Shift combos (DevTools, inspect, console, task manager)
                if (e.shiftKey) {
                    const blockedShift = ['i', 'j', 'c', 'k', 'm', 'b', 'n', 's', 'delete'];
                    if (blockedShift.includes(key)) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        logViolation('keyboard_shortcut', `Blocked Ctrl+Shift+${e.key.toUpperCase()}`);
                        return false;
                    }
                }

                // Allow Ctrl+A only inside essay textarea
                if (key === 'a') {
                    const isEssay = e.target && e.target.classList && e.target.classList.contains('essay-textarea');
                    if (isEssay) return;
                }

                // Allow Ctrl+Z/Y (undo/redo) inside essay textarea
                if ((key === 'z' || key === 'y') && e.target && e.target.classList && e.target.classList.contains('essay-textarea')) {
                    return;
                }

                // Allow Ctrl+Backspace and Ctrl+Delete (word delete in essay)
                if ((key === 'backspace' || key === 'delete') && e.target && e.target.classList && e.target.classList.contains('essay-textarea')) {
                    return;
                }

                // Blocked Ctrl + single key (comprehensive list)
                const blockedCtrl = ['c', 'v', 'x', 'a', 'p', 's', 'f', 'u', 'g', 'h', 'l', 'n', 'w', 't', 'd', 'e', 'k', 'o', 'r', 'j', 'b', 'q', 'i'];
                if (blockedCtrl.includes(key)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    logViolation('keyboard_shortcut', `Blocked Ctrl+${e.key.toUpperCase()}`);
                    return false;
                }

                // Block Ctrl+Number (switch tabs)
                if (key >= '0' && key <= '9') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    logViolation('keyboard_shortcut', `Blocked Ctrl+${key}`);
                    return false;
                }
            }

            // ── Alt + key ────────────────────────────────
            if (e.altKey) {
                // Block all Alt combinations that could switch windows/tabs
                const blockedAlt = ['F4', 'Tab', 'd', 'D', 'Home', 'Left', 'Right', 'ArrowLeft', 'ArrowRight', 'F5', 'F6', 'Escape'];
                if (blockedAlt.includes(e.key)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    logViolation('keyboard_shortcut', `Blocked Alt+${e.key}`);
                    return false;
                }
            }

            // ── Windows key / Meta key alone ─────────────
            if (e.key === 'Meta' || e.key === 'OS') {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }

        async function autoSubmit() {
            if (isSubmitting) return;
            isSubmitting = true;
            
            // Try to sync any remaining offline answers before submit
            if (offlineQueue.length > 0 && isOnline) {
                await syncOfflineAnswers();
            }
            
            try {
                const response = await fetch(config.endpoints.autoSubmit, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const data = await response.json().catch(() => null);
                if (data && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (!response.ok) {
                    throw new Error((data && data.message) || 'Auto-submit gagal.');
                }

                // Defensive fallback if server responds without redirect.
                window.location.href = '{{ route("student.exams.result", $attempt) }}';
            } catch (e) {
                if (window._origConsoleError) window._origConsoleError('[AutoSubmit]', e);

                // Last fallback: try normal submit flow.
                document.getElementById('submit-form').submit();
            }
        }

        // Cleanup
        window.addEventListener('beforeunload', function(e) {
            // If submitting, let it go without warning
            if (isSubmitting) {
                if (stream) stream.getTracks().forEach(track => track.stop());
                return;
            }
            
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
            isSubmitting = true;
            
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
