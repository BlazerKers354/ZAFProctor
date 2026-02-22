@extends('layouts.student')

@section('title', 'Persiapan Ujian')
@section('page-title', 'Persiapan Ujian')

@push('styles')
<style>
    /* ====== Page Layout ====== */
    .precheck-wrapper {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
        align-items: start;
        min-height: calc(100vh - 200px);
    }
    @media (max-width: 991.98px) {
        .precheck-wrapper {
            grid-template-columns: 1fr;
        }
        .camera-sticky-col {
            position: relative !important;
            top: 0 !important;
        }
    }
    @media (min-width: 1200px) {
        .precheck-wrapper {
            grid-template-columns: 380px 1fr;
        }
    }

    /* ====== Camera Panel (Sticky) ====== */
    .camera-sticky-col {
        position: sticky;
        top: 90px;
        z-index: 10;
    }
    .camera-panel {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    .camera-panel-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .camera-panel-header h6 {
        color: #fff;
        margin: 0;
        font-weight: 600;
        font-size: 14px;
    }
    .camera-panel-header i {
        color: #fff;
        font-size: 18px;
    }
    .camera-preview-container {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        background: #0f172a;
        overflow: hidden;
    }
    .camera-preview-container video {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
        z-index: 1;
    }
    .camera-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none;
        z-index: 2;
        transform: scaleX(-1);
    }
    .face-guide {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 140px; height: 180px;
        border: 2.5px dashed rgba(255,255,255,0.45);
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
        pointer-events: none;
        z-index: 3;
        transition: all 0.3s ease;
    }
    .face-guide.detected {
        border-color: #22c55e;
        border-style: solid;
        box-shadow: 0 0 20px rgba(34,197,94,0.25);
    }
    #camera-off-state {
        z-index: 10;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    #camera-off-state .btn {
        border-radius: 12px;
        padding: 8px 20px;
        font-size: 13px;
    }

    /* Camera status bar */
    .camera-status-bar {
        padding: 10px 16px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid #e2e8f0;
        min-height: 44px;
    }
    .camera-status-bar.status-pending { background: #eff6ff; color: #3b82f6; }
    .camera-status-bar.status-success { background: #f0fdf4; color: #16a34a; }
    .camera-status-bar.status-error { background: #fef2f2; color: #dc2626; }
    .camera-status-bar.status-warning { background: #fffbeb; color: #d97706; }
    .camera-status-bar .spinner-border { width: 14px; height: 14px; border-width: 2px; }

    /* Camera tips compact */
    .camera-tips {
        padding: 12px 16px;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
    }
    .camera-tips summary {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .camera-tips ul {
        margin: 8px 0 0;
        padding-left: 18px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.7;
    }

    /* ====== Right Content ====== */
    .right-content-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Exam info bar */
    .exam-info-bar {
        background: #fff;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .exam-info-bar .exam-title-section {
        flex: 1;
        min-width: 200px;
    }
    .exam-info-bar .exam-title-section h5 {
        margin: 0 0 2px;
        font-weight: 700;
        font-size: 17px;
        color: #1e293b;
    }
    .exam-info-bar .exam-title-section span {
        font-size: 13px;
        color: #64748b;
    }
    .exam-stat-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .exam-stat-badge {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 8px 14px;
        text-align: center;
        min-width: 80px;
    }
    .exam-stat-badge small {
        display: block;
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .exam-stat-badge strong {
        font-size: 14px;
        color: #334155;
    }

    /* ====== System Checks Grid ====== */
    .checks-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (max-width: 575.98px) {
        .checks-grid {
            grid-template-columns: 1fr;
        }
    }
    .check-card {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .check-card.success {
        border-color: #86efac;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .check-card.error {
        border-color: #fca5a5;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .check-card.warning {
        border-color: #fcd34d;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }
    .check-card.pending {
        border-color: #93c5fd;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .check-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .check-icon.pending { background: #dbeafe; color: #3b82f6; }
    .check-icon.success { background: #bbf7d0; color: #16a34a; }
    .check-icon.error { background: #fecaca; color: #dc2626; }
    .check-icon.warning { background: #fde68a; color: #d97706; }
    .check-card .check-info h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }
    .check-card .check-info small {
        font-size: 11.5px;
        color: #64748b;
        line-height: 1.3;
    }
    .check-card .check-action {
        margin-left: auto;
        flex-shrink: 0;
    }
    .check-card .check-action .badge {
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .check-card .check-action .btn {
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 8px;
    }

    /* ====== Token Form ====== */
    .token-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 2px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    .token-section.ready {
        border-color: #22c55e;
        box-shadow: 0 4px 20px rgba(34,197,94,0.12);
    }
    .token-section.not-ready {
        border-color: #e2e8f0;
    }
    .token-section-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .token-section-header .token-icon-wrap {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }
    .token-section-header .token-icon-wrap.locked {
        background: #fee2e2;
        color: #dc2626;
    }
    .token-section-header .token-icon-wrap.unlocked {
        background: #dcfce7;
        color: #16a34a;
    }
    .token-section-header .token-header-text h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }
    .token-section-header .token-header-text p {
        margin: 0;
        font-size: 12.5px;
        color: #94a3b8;
    }
    .token-section-body {
        padding: 20px 24px 24px;
    }
    .token-section .form-control {
        border-radius: 12px;
        font-size: 15px;
        padding: 10px 16px;
        border-color: #e2e8f0;
    }
    .token-section .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .token-section .input-group-text {
        border-radius: 12px 0 0 12px;
        border-color: #e2e8f0;
    }
    .token-section .btn-start-exam {
        border-radius: 12px;
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .token-section .btn-start-exam:not(:disabled) {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(79,70,229,0.3);
    }
    .token-section .btn-start-exam:not(:disabled):hover {
        box-shadow: 0 6px 20px rgba(79,70,229,0.4);
        transform: translateY(-1px);
    }

    /* ====== Animations ====== */
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .spin-animation {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up {
        animation: fadeInUp 0.4s ease;
    }
</style>
@endpush

@section('content')
<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm" style="border-radius: 10px;">
        <i class="ph ph-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="precheck-wrapper">
    {{-- ========== LEFT: Sticky Camera Panel ========== --}}
    <div class="camera-sticky-col">
        <div class="camera-panel fade-in-up">
            <div class="camera-panel-header">
                <i class="ph ph-camera"></i>
                <h6>Verifikasi Wajah</h6>
            </div>

            <!-- Camera Preview -->
            <div class="camera-preview-container">
                <video id="camera-preview" autoplay muted playsinline></video>
                <canvas id="face-canvas" class="camera-overlay"></canvas>
                <div id="face-guide" class="face-guide"></div>

                <!-- Camera Off State -->
                <div id="camera-off-state" class="position-absolute top-0 start-0 end-0 bottom-0 d-flex flex-column align-items-center justify-content-center text-white">
                    <i class="ph ph-camera-slash mb-2" style="font-size: 36px; opacity: 0.45;"></i>
                    <p class="mb-2 opacity-75" style="font-size: 13px;">Kamera belum aktif</p>
                    <button type="button" class="btn btn-primary btn-sm" onclick="requestCameraAccess()">
                        <i class="ph ph-camera me-1"></i>Aktifkan Kamera
                    </button>
                </div>
            </div>

            <!-- Face Status Bar (compact) -->
            <div id="face-status" class="camera-status-bar status-pending">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span>Menunggu aktivasi kamera...</span>
            </div>

            <!-- Tips (collapsible) -->
            <div class="camera-tips">
                <details>
                    <summary><i class="ph ph-lightbulb"></i> Tips Verifikasi Wajah</summary>
                    <ul class="mb-0">
                        <li>Posisikan wajah dalam area panduan oval</li>
                        <li>Pastikan pencahayaan cukup terang</li>
                        <li>Lepas kacamata/masker jika perlu</li>
                        <li>Jangan bergerak terlalu cepat</li>
                    </ul>
                </details>
            </div>
        </div>
    </div>

    {{-- ========== RIGHT: Content ========== --}}
    <div class="right-content-col">
        <!-- Exam Info Bar -->
        <div class="exam-info-bar fade-in-up">
            <div class="d-flex align-items-center gap-3 exam-title-section">
                <div style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;background:linear-gradient(135deg,#eef2ff,#e0e7ff);color:#4f46e5;">
                    <i class="ph ph-exam"></i>
                </div>
                <div>
                    <h5>{{ $exam->title }}</h5>
                    <span>{{ $exam->course?->name ?? 'Ujian Umum' }}</span>
                </div>
            </div>
            <div class="exam-stat-badges">
                <div class="exam-stat-badge">
                    <small>Durasi</small>
                    <strong>{{ $exam->duration }} min</strong>
                </div>
                <div class="exam-stat-badge">
                    <small>Soal</small>
                    <strong>{{ $exam->question_count }}</strong>
                </div>
                <div class="exam-stat-badge">
                    <small>KKM</small>
                    <strong>{{ $exam->settings->passing_score ?? 60 }}%</strong>
                </div>
            </div>
        </div>

        <!-- System Checks -->
        <div class="fade-in-up" style="animation-delay: 0.1s;">
            <h6 class="mb-3 f-w-600" style="font-size: 14px; color: #475569;">
                <i class="ph ph-check-circle me-1"></i>Pemeriksaan Sistem
            </h6>
            <div class="checks-grid">
                <!-- Camera Check -->
                <div id="check-camera" class="check-card pending">
                    <div id="icon-camera" class="check-icon pending">
                        <i class="ph ph-camera"></i>
                    </div>
                    <div class="check-info">
                        <h6>Akses Kamera</h6>
                        <small id="status-camera">Menunggu izin kamera...</small>
                    </div>
                    <div class="check-action" id="action-camera">
                        <button class="btn btn-sm btn-primary" onclick="requestCameraAccess()">
                            <i class="ph ph-play"></i>
                        </button>
                    </div>
                </div>

                <!-- Face Detection Check -->
                <div id="check-face" class="check-card pending">
                    <div id="icon-face" class="check-icon pending">
                        <i class="ph ph-user-focus"></i>
                    </div>
                    <div class="check-info">
                        <h6>Deteksi Wajah</h6>
                        <small id="status-face">Menunggu kamera aktif...</small>
                    </div>
                    <div class="check-action" id="action-face">
                        <span class="badge bg-secondary">Menunggu</span>
                    </div>
                </div>

                <!-- Browser Compatibility Check -->
                <div id="check-browser" class="check-card pending">
                    <div id="icon-browser" class="check-icon pending">
                        <i class="ph ph-browser"></i>
                    </div>
                    <div class="check-info">
                        <h6>Browser</h6>
                        <small id="status-browser">Memeriksa...</small>
                    </div>
                    <div class="check-action" id="action-browser"></div>
                </div>

                <!-- Fullscreen Support Check -->
                <div id="check-fullscreen" class="check-card pending">
                    <div id="icon-fullscreen" class="check-icon pending">
                        <i class="ph ph-corners-out"></i>
                    </div>
                    <div class="check-info">
                        <h6>Fullscreen</h6>
                        <small id="status-fullscreen">Memeriksa...</small>
                    </div>
                    <div class="check-action" id="action-fullscreen"></div>
                </div>
            </div>
        </div>

        <!-- Token Entry Section -->
        <div id="token-form-container" class="token-section not-ready fade-in-up" style="animation-delay: 0.2s;">
            <div class="token-section-header">
                <div id="form-icon" class="token-icon-wrap locked">
                    <i class="ph ph-lock"></i>
                </div>
                <div class="token-header-text">
                    <h6>Masukkan Token Ujian</h6>
                    <p id="form-status-text">Lengkapi semua pemeriksaan terlebih dahulu</p>
                </div>
            </div>

            <div class="token-section-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="border-radius: 10px; font-size: 13px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="ph ph-warning-circle mt-1"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form id="start-exam-form" action="{{ route('student.exams.start', $exam) }}" method="POST">
                    @csrf
                    <input type="hidden" name="pre_check_passed" value="1">
                    <input type="hidden" name="camera_verified" id="camera-verified-input" value="0">
                    <input type="hidden" name="face_verified" id="face-verified-input" value="0">

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ph ph-key text-muted"></i>
                            </span>
                            <input type="text"
                                   name="access_token"
                                   id="access_token"
                                   placeholder="Masukkan token dari pengawas"
                                   class="form-control border-start-0 @error('access_token') is-invalid @enderror"
                                   disabled
                                   autocomplete="off">
                        </div>
                        @error('access_token')
                            <div class="text-danger mt-1" style="font-size: 12px;">
                                <i class="ph ph-warning me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree" name="agree" disabled>
                            <label class="form-check-label" for="agree" style="font-size: 12.5px; color: #64748b;">
                                Saya memahami bahwa ujian ini
                                @if($exam->settings?->webcam_enabled) dipantau dengan kamera @endif
                                @if($exam->settings?->detect_copy_paste || $exam->settings?->detect_tab_switch || $exam->settings?->detect_right_click) dan aktivitas saya akan diawasi @endif
                                — pelanggaran akan dicatat
                                @if($exam->settings?->auto_submit_threshold) (maks {{ $exam->settings->auto_submit_threshold }} pelanggaran) @endif
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="start-btn" class="btn btn-primary btn-start-exam w-100" disabled>
                        <i class="ph ph-lock me-2"></i>
                        <span id="start-btn-text">Selesaikan Pemeriksaan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Face-api.js -->
<script src="{{ asset('assets/proctoring/face-api.min.js') }}"></script>

<script>
// Configuration
const CONFIG = {
    modelPath: '{{ asset("assets/proctoring/models") }}',
    faceDetectionInterval: 500,
    faceConfirmedThreshold: 3,
    maxMultipleFacesAllowed: 1,
};

// Check if returning from token error
const hasTokenError = {{ $errors->has('access_token') ? 'true' : 'false' }};
const hasAnyError = {{ $errors->any() ? 'true' : 'false' }};
const previousPreCheckPassed = {{ session('pre_check_passed') ? 'true' : 'false' }};
const previousCameraVerified = {{ session('camera_verified') ? 'true' : 'false' }};
const previousFaceVerified = {{ session('face_verified') ? 'true' : 'false' }};

// State
let stream = null;
let faceDetectionInterval = null;
let faceConfirmedCount = 0;
let allChecksPass = false;

const checks = {
    camera: false,
    face: false,
    browser: false,
    fullscreen: false
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    checkBrowserCompatibility();
    checkFullscreenSupport();
    await loadFaceApiModels();

    if ((hasTokenError || hasAnyError) && (previousPreCheckPassed || previousCameraVerified)) {
        console.log('Returning from error, auto-starting camera...');
        setTimeout(() => { requestCameraAccess(); }, 500);
    }
});

// Load face-api.js models
async function loadFaceApiModels() {
    try {
        updateCheckStatus('face', 'pending', 'Memuat model deteksi wajah...');
        await faceapi.nets.tinyFaceDetector.loadFromUri(CONFIG.modelPath);
        console.log('Face-api models loaded successfully');
        updateCheckStatus('face', 'pending', 'Model siap. Menunggu kamera aktif...');
    } catch (error) {
        console.error('Error loading face-api models:', error);
        updateCheckStatus('face', 'error', 'Gagal memuat model deteksi wajah');
    }
}

// Request camera access
async function requestCameraAccess() {
    try {
        updateCheckStatus('camera', 'pending', 'Meminta izin akses kamera...');

        stream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
            audio: false
        });

        const video = document.getElementById('camera-preview');
        video.srcObject = stream;

        const cameraOffState = document.getElementById('camera-off-state');
        cameraOffState.classList.remove('d-flex');
        cameraOffState.classList.add('d-none');

        video.onloadedmetadata = () => {
            video.play();
            setupFaceCanvas();
            startFaceDetection();
        };

        updateCheckStatus('camera', 'success', 'Kamera aktif dan berfungsi');
        checks.camera = true;
        document.getElementById('action-camera').innerHTML = '<span class="badge bg-success"><i class="ph ph-check me-1"></i>Aktif</span>';
        validateAllChecks();

    } catch (error) {
        console.error('Camera access error:', error);
        let errorMessage = 'Gagal mengakses kamera';

        if (error.name === 'NotAllowedError') {
            errorMessage = 'Izin kamera ditolak. Harap izinkan akses kamera.';
        } else if (error.name === 'NotFoundError') {
            errorMessage = 'Tidak ada kamera yang ditemukan.';
        } else if (error.name === 'NotReadableError') {
            errorMessage = 'Kamera sedang digunakan aplikasi lain.';
        }

        updateCheckStatus('camera', 'error', errorMessage);
        document.getElementById('action-camera').innerHTML = '<button class="btn btn-sm btn-danger" onclick="requestCameraAccess()"><i class="ph ph-arrow-clockwise me-1"></i>Coba Lagi</button>';
    }
}

// Setup face detection canvas
function setupFaceCanvas() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('face-canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
}

// Start face detection loop
function startFaceDetection() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('face-canvas');
    const ctx = canvas.getContext('2d');
    const faceGuide = document.getElementById('face-guide');

    updateFaceStatus('pending', 'Memindai wajah...');

    faceDetectionInterval = setInterval(async () => {
        if (!video.videoWidth || video.paused || video.ended) return;

        try {
            const detections = await faceapi.detectAllFaces(
                video,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 })
            );

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (detections.length === 0) {
                faceConfirmedCount = 0;
                updateFaceStatus('error', 'Wajah tidak terdeteksi. Posisikan wajah dalam area panduan.');
                faceGuide.classList.remove('detected');
                checks.face = false;

            } else if (detections.length === 1) {
                const detection = detections[0];
                const box = detection.box;

                ctx.save();
                ctx.scale(-1, 1);
                ctx.translate(-canvas.width, 0);
                ctx.strokeStyle = '#22c55e';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
                ctx.fillStyle = '#22c55e';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(Math.round(detection.score * 100) + '%', box.x, box.y - 8);
                ctx.restore();

                faceConfirmedCount++;
                faceGuide.classList.add('detected');

                if (faceConfirmedCount >= CONFIG.faceConfirmedThreshold) {
                    updateFaceStatus('success', 'Wajah terdeteksi dengan baik (' + Math.round(detection.score * 100) + '% confidence)');
                    checks.face = true;
                    document.getElementById('face-verified-input').value = '1';
                    document.getElementById('action-face').innerHTML = '<span class="badge bg-success"><i class="ph ph-check me-1"></i>Terverifikasi</span>';
                } else {
                    updateFaceStatus('warning', 'Memverifikasi wajah... (' + faceConfirmedCount + '/' + CONFIG.faceConfirmedThreshold + ')');
                }

            } else {
                faceConfirmedCount = 0;
                updateFaceStatus('error', 'Terdeteksi ' + detections.length + ' wajah. Pastikan hanya ada 1 orang di depan kamera.');
                faceGuide.classList.remove('detected');
                checks.face = false;

                ctx.save();
                ctx.scale(-1, 1);
                ctx.translate(-canvas.width, 0);
                detections.forEach(detection => {
                    const box = detection.box;
                    ctx.strokeStyle = '#ef4444';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);
                });
                ctx.restore();
            }

            validateAllChecks();

        } catch (error) {
            console.error('Face detection error:', error);
        }
    }, CONFIG.faceDetectionInterval);
}

// Update face detection status (compact status bar)
function updateFaceStatus(status, message) {
    const statusEl = document.getElementById('face-status');
    statusEl.className = 'camera-status-bar status-' + status;

    let icon = '';
    switch(status) {
        case 'pending': icon = '<div class="spinner-border spinner-border-sm" role="status"></div>'; break;
        case 'warning': icon = '<i class="ph ph-warning" style="font-size:16px;"></i>'; break;
        case 'success': icon = '<i class="ph ph-check-circle" style="font-size:16px;"></i>'; break;
        case 'error':   icon = '<i class="ph ph-x-circle" style="font-size:16px;"></i>'; break;
    }

    statusEl.innerHTML = icon + '<span>' + message + '</span>';
}

// Update check status
function updateCheckStatus(checkId, status, message) {
    const checkEl = document.getElementById('check-' + checkId);
    const iconEl = document.getElementById('icon-' + checkId);
    const statusTextEl = document.getElementById('status-' + checkId);

    checkEl.className = 'check-card ' + status;
    iconEl.className = 'check-icon ' + status;
    statusTextEl.textContent = message;
}

// Check browser compatibility
function checkBrowserCompatibility() {
    const isCompatible =
        'mediaDevices' in navigator &&
        'getUserMedia' in navigator.mediaDevices &&
        typeof HTMLCanvasElement !== 'undefined';

    if (isCompatible) {
        updateCheckStatus('browser', 'success', 'Browser mendukung semua fitur yang diperlukan');
        document.getElementById('action-browser').innerHTML = '<span class="badge bg-success"><i class="ph ph-check me-1"></i>Compatible</span>';
        checks.browser = true;
    } else {
        updateCheckStatus('browser', 'error', 'Browser tidak mendukung. Gunakan Chrome, Firefox, atau Edge terbaru.');
        document.getElementById('action-browser').innerHTML = '<span class="badge bg-danger">Tidak Compatible</span>';
        checks.browser = false;
    }
    validateAllChecks();
}

// Check fullscreen support
function checkFullscreenSupport() {
    const isSupported =
        document.fullscreenEnabled ||
        document.webkitFullscreenEnabled ||
        document.mozFullScreenEnabled;

    if (isSupported) {
        updateCheckStatus('fullscreen', 'success', 'Mode fullscreen didukung');
        document.getElementById('action-fullscreen').innerHTML = '<span class="badge bg-success"><i class="ph ph-check me-1"></i>Didukung</span>';
        checks.fullscreen = true;
    } else {
        updateCheckStatus('fullscreen', 'warning', 'Mode fullscreen mungkin tidak didukung sepenuhnya');
        document.getElementById('action-fullscreen').innerHTML = '<span class="badge bg-warning">Sebagian</span>';
        checks.fullscreen = true;
    }
    validateAllChecks();
}

// Validate all checks and toggle form
function validateAllChecks() {
    allChecksPass = checks.camera && checks.face && checks.browser && checks.fullscreen;

    const formContainer = document.getElementById('token-form-container');
    const formIcon = document.getElementById('form-icon');
    const formStatusText = document.getElementById('form-status-text');
    const tokenInput = document.getElementById('access_token');
    const agreeCheckbox = document.getElementById('agree');
    const startBtn = document.getElementById('start-btn');
    const startBtnText = document.getElementById('start-btn-text');
    const cameraVerifiedInput = document.getElementById('camera-verified-input');

    if (allChecksPass) {
        formContainer.classList.remove('not-ready');
        formContainer.classList.add('ready');

        formIcon.className = 'token-icon-wrap unlocked';
        formIcon.innerHTML = '<i class="ph ph-lock-open"></i>';

        formStatusText.textContent = 'Semua pemeriksaan selesai. Masukkan token ujian.';
        formStatusText.style.color = '#16a34a';

        tokenInput.disabled = false;
        agreeCheckbox.disabled = false;

        agreeCheckbox.addEventListener('change', function() {
            if (this.checked && tokenInput.value.trim().length > 0) {
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="ph ph-play me-2"></i><span>Mulai Ujian</span>';
            } else {
                startBtn.disabled = true;
            }
        });

        tokenInput.addEventListener('input', function() {
            if (agreeCheckbox.checked && this.value.trim().length > 0) {
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="ph ph-play me-2"></i><span>Mulai Ujian</span>';
            } else {
                startBtn.disabled = true;
            }
        });

        cameraVerifiedInput.value = '1';

    } else {
        formContainer.classList.add('not-ready');
        formContainer.classList.remove('ready');

        formIcon.className = 'token-icon-wrap locked';
        formIcon.innerHTML = '<i class="ph ph-lock"></i>';

        const failingChecks = [];
        if (!checks.camera) failingChecks.push('Kamera');
        if (!checks.face) failingChecks.push('Deteksi Wajah');
        if (!checks.browser) failingChecks.push('Browser');
        if (!checks.fullscreen) failingChecks.push('Fullscreen');

        if (failingChecks.length > 0) {
            formStatusText.textContent = 'Perlu diselesaikan: ' + failingChecks.join(', ');
        } else {
            formStatusText.textContent = 'Lengkapi semua pemeriksaan terlebih dahulu';
        }
        formStatusText.style.color = '#94a3b8';

        tokenInput.disabled = true;
        agreeCheckbox.disabled = true;
        startBtn.disabled = true;
        startBtnText.textContent = 'Selesaikan Pemeriksaan';
        cameraVerifiedInput.value = '0';
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (faceDetectionInterval) { clearInterval(faceDetectionInterval); }
    if (stream) { stream.getTracks().forEach(track => track.stop()); }
});
</script>
@endsection
