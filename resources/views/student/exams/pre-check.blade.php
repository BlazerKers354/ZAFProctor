@extends('layouts.student')

@section('title', 'Persiapan Ujian')
@section('page-title', 'Persiapan Ujian')

@push('styles')
<style>
    .check-item {
        transition: all 0.3s ease;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .check-item.success {
        border-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .check-item.error {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .check-item.warning {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }
    .check-item.pending {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    
    .status-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .status-icon.pending { background: #dbeafe; color: #3b82f6; }
    .status-icon.success { background: #dcfce7; color: #22c55e; }
    .status-icon.error { background: #fee2e2; color: #ef4444; }
    .status-icon.warning { background: #fef3c7; color: #f59e0b; }
    
    .camera-preview-container {
        position: relative;
        width: 100%;
        max-width: 480px;
        aspect-ratio: 4/3;
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        margin: 0 auto;
    }
    .camera-preview-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
        z-index: 1;
    }
    .camera-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 2;
        transform: scaleX(-1);
    }
    .face-detection-box {
        position: absolute;
        border: 3px solid #22c55e;
        border-radius: 8px;
        background: rgba(34, 197, 94, 0.1);
        transition: all 0.1s ease;
    }
    .face-detection-box.warning {
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.1);
    }
    .face-detection-box.danger {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }
    
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
    
    .face-guide {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        height: 230px;
        border: 3px dashed rgba(255,255,255,0.5);
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
        pointer-events: none;
        z-index: 3;
    }
    .face-guide.detected {
        border-color: #22c55e;
        border-style: solid;
    }
    
    #camera-off-state {
        z-index: 10;
    }
    
    .instructions-box {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 24px;
        color: #fff;
    }
    .instructions-box h5 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .instructions-box ul {
        color: #cbd5e1;
        padding-left: 20px;
    }
    .instructions-box ul li {
        margin-bottom: 8px;
    }
    
    .token-form {
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
    }
    .token-form.ready {
        border-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .token-form.not-ready {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
</style>
@endpush

@section('content')
<!-- Back Button -->
<div class="mb-4">
    <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm">
        <i class="ph ph-arrow-left me-1"></i>Kembali ke Daftar Ujian
    </a>
</div>

<div class="row">
    <!-- Left Column: Camera & Face Detection -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph ph-camera f-22"></i>
                    <h5 class="mb-0 text-white f-w-600">Verifikasi Wajah</h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Camera Preview -->
                <div class="camera-preview-container mb-4">
                    <video id="camera-preview" autoplay muted playsinline></video>
                    <canvas id="face-canvas" class="camera-overlay"></canvas>
                    <div id="face-guide" class="face-guide"></div>
                    
                    <!-- Camera Off State -->
                    <div id="camera-off-state" class="position-absolute top-0 start-0 end-0 bottom-0 d-flex flex-column align-items-center justify-content-center bg-dark text-white">
                        <i class="ph ph-camera-slash f-48 mb-3 opacity-50"></i>
                        <p class="mb-3 opacity-75">Kamera belum aktif</p>
                        <button type="button" class="btn btn-primary" onclick="requestCameraAccess()">
                            <i class="ph ph-camera me-1"></i>Aktifkan Kamera
                        </button>
                    </div>
                </div>
                
                <!-- Face Detection Status -->
                <div id="face-status" class="alert alert-info mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span>Menunggu aktivasi kamera...</span>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="instructions-box">
                    <h5><i class="ph ph-info me-2"></i>Petunjuk Verifikasi Wajah</h5>
                    <ul class="mb-0">
                        <li>Pastikan wajah Anda berada dalam area panduan (oval)</li>
                        <li>Lepaskan kacamata atau masker jika diperlukan</li>
                        <li>Pastikan pencahayaan cukup terang</li>
                        <li>Jangan bergerak terlalu cepat</li>
                        <li>Tunggu hingga status berubah menjadi "Wajah Terdeteksi"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: System Checks & Token Entry -->
    <div class="col-lg-6">
        <!-- Exam Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="ph ph-exam f-28 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 f-w-600">{{ $exam->title }}</h5>
                        <span class="text-muted">{{ $exam->course?->name ?? 'Ujian Umum' }}</span>
                    </div>
                </div>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="bg-light rounded-3 p-2">
                            <small class="text-muted d-block">Durasi</small>
                            <strong>{{ $exam->duration }} menit</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-light rounded-3 p-2">
                            <small class="text-muted d-block">Jumlah Soal</small>
                            <strong>{{ $exam->question_count }} soal</strong>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-light rounded-3 p-2">
                            <small class="text-muted d-block">KKM</small>
                            <strong>{{ $exam->settings->passing_score ?? 60 }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Checks -->
        <div class="card mb-4">
            <div class="card-header py-3">
                <h5 class="mb-0 f-w-600"><i class="ph ph-check-circle me-2"></i>Pemeriksaan Sistem</h5>
            </div>
            <div class="card-body p-0">
                <!-- Camera Check -->
                <div id="check-camera" class="check-item pending m-3 mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div id="icon-camera" class="status-icon pending">
                            <i class="ph ph-camera"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 f-w-600">Akses Kamera</h6>
                            <small id="status-camera" class="text-muted">Menunggu izin akses kamera...</small>
                        </div>
                        <div id="action-camera">
                            <button class="btn btn-sm btn-primary" onclick="requestCameraAccess()">
                                <i class="ph ph-play me-1"></i>Aktifkan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Face Detection Check -->
                <div id="check-face" class="check-item pending m-3 mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div id="icon-face" class="status-icon pending">
                            <i class="ph ph-user-focus"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 f-w-600">Deteksi Wajah</h6>
                            <small id="status-face" class="text-muted">Menunggu kamera aktif...</small>
                        </div>
                        <div id="action-face">
                            <span class="badge bg-secondary">Menunggu</span>
                        </div>
                    </div>
                </div>
                
                <!-- Browser Compatibility Check -->
                <div id="check-browser" class="check-item pending m-3 mb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div id="icon-browser" class="status-icon pending">
                            <i class="ph ph-browser"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 f-w-600">Kompatibilitas Browser</h6>
                            <small id="status-browser" class="text-muted">Memeriksa...</small>
                        </div>
                        <div id="action-browser"></div>
                    </div>
                </div>
                
                <!-- Fullscreen Support Check -->
                <div id="check-fullscreen" class="check-item pending m-3">
                    <div class="d-flex align-items-center gap-3">
                        <div id="icon-fullscreen" class="status-icon pending">
                            <i class="ph ph-corners-out"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 f-w-600">Mode Fullscreen</h6>
                            <small id="status-fullscreen" class="text-muted">Memeriksa...</small>
                        </div>
                        <div id="action-fullscreen"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Token Entry Form -->
        <div id="token-form-container" class="token-form not-ready">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ph ph-warning-circle f-22 mt-1"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="text-center mb-4">
                <div id="form-icon" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 p-3 mb-3">
                    <i class="ph ph-lock f-32 text-danger"></i>
                </div>
                <h5 class="f-w-600 mb-2">Masukkan Token Ujian</h5>
                <p id="form-status-text" class="text-muted mb-0">
                    Lengkapi semua pemeriksaan di atas terlebih dahulu
                </p>
            </div>
            
            <form id="start-exam-form" action="{{ route('student.exams.start', $exam) }}" method="POST">
                @csrf
                <input type="hidden" name="pre_check_passed" value="1">
                <input type="hidden" name="camera_verified" id="camera-verified-input" value="0">
                <input type="hidden" name="face_verified" id="face-verified-input" value="0">
                
                <div class="mb-3">
                    <label for="access_token" class="form-label f-w-500">Token Akses</label>
                    <div class="input-group input-group-lg">
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
                        <div class="alert alert-danger mt-2 mb-0 py-2">
                            <i class="ph ph-warning me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree" name="agree" disabled>
                        <label class="form-check-label f-14" for="agree">
                            Saya memahami bahwa ujian ini akan dipantau dengan kamera dan pelanggaran akan dicatat
                        </label>
                    </div>
                </div>
                
                <button type="submit" id="start-btn" class="btn btn-primary btn-lg w-100" disabled>
                    <i class="ph ph-lock me-2"></i>
                    <span id="start-btn-text">Selesaikan Pemeriksaan Terlebih Dahulu</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Face-api.js -->
<script src="{{ asset('assets/proctoring/face-api.min.js') }}"></script>

<script>
// Configuration
const CONFIG = {
    modelPath: '{{ asset("assets/proctoring/models") }}',
    faceDetectionInterval: 500, // ms
    faceConfirmedThreshold: 3, // Number of consecutive detections to confirm
    maxMultipleFacesAllowed: 1,
};

// Check if returning from token error (need to restore pre-check state)
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
    // Check browser compatibility first
    checkBrowserCompatibility();
    
    // Check fullscreen support
    checkFullscreenSupport();
    
    // Load face-api models
    await loadFaceApiModels();
    
    // If returning from error (token or authorization), auto-start camera verification
    if ((hasTokenError || hasAnyError) && (previousPreCheckPassed || previousCameraVerified)) {
        console.log('Returning from error, auto-starting camera...');
        setTimeout(() => {
            requestCameraAccess();
        }, 500);
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
            video: { 
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false 
        });
        
        const video = document.getElementById('camera-preview');
        video.srcObject = stream;
        
        // Hide camera off state - use classList to properly override Bootstrap's d-flex !important
        const cameraOffState = document.getElementById('camera-off-state');
        cameraOffState.classList.remove('d-flex');
        cameraOffState.classList.add('d-none');
        
        // Wait for video to be ready
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
        document.getElementById('action-camera').innerHTML = `
            <button class="btn btn-sm btn-danger" onclick="requestCameraAccess()">
                <i class="ph ph-arrow-clockwise me-1"></i>Coba Lagi
            </button>
        `;
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
            
            // Clear previous drawings
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            if (detections.length === 0) {
                // No face detected
                faceConfirmedCount = 0;
                updateFaceStatus('error', 'Wajah tidak terdeteksi. Posisikan wajah dalam area panduan.');
                faceGuide.classList.remove('detected');
                checks.face = false;
                
            } else if (detections.length === 1) {
                // One face detected - good
                const detection = detections[0];
                const box = detection.box;
                
                // Draw face box with mirror transform
                ctx.save();
                ctx.scale(-1, 1);
                ctx.translate(-canvas.width, 0);
                
                ctx.strokeStyle = '#22c55e';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
                
                // Draw confidence score
                ctx.fillStyle = '#22c55e';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(`${Math.round(detection.score * 100)}%`, box.x, box.y - 8);
                
                ctx.restore();
                
                faceConfirmedCount++;
                faceGuide.classList.add('detected');
                
                if (faceConfirmedCount >= CONFIG.faceConfirmedThreshold) {
                    updateFaceStatus('success', `Wajah terdeteksi dengan baik (${Math.round(detection.score * 100)}% confidence)`);
                    checks.face = true;
                    document.getElementById('face-verified-input').value = '1';
                    document.getElementById('action-face').innerHTML = '<span class="badge bg-success"><i class="ph ph-check me-1"></i>Terverifikasi</span>';
                } else {
                    updateFaceStatus('warning', `Memverifikasi wajah... (${faceConfirmedCount}/${CONFIG.faceConfirmedThreshold})`);
                }
                
            } else {
                // Multiple faces detected - warning
                faceConfirmedCount = 0;
                updateFaceStatus('error', `Terdeteksi ${detections.length} wajah. Pastikan hanya ada 1 orang di depan kamera.`);
                faceGuide.classList.remove('detected');
                checks.face = false;
                
                // Draw all detected faces in red
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

// Update face detection status
function updateFaceStatus(status, message) {
    const statusEl = document.getElementById('face-status');
    
    statusEl.className = 'alert mb-4';
    
    switch(status) {
        case 'pending':
            statusEl.classList.add('alert-info');
            statusEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>${message}</span>
                </div>
            `;
            break;
        case 'warning':
            statusEl.classList.add('alert-warning');
            statusEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="ph ph-warning f-18"></i>
                    <span>${message}</span>
                </div>
            `;
            break;
        case 'success':
            statusEl.classList.add('alert-success');
            statusEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="ph ph-check-circle f-18"></i>
                    <span>${message}</span>
                </div>
            `;
            break;
        case 'error':
            statusEl.classList.add('alert-danger');
            statusEl.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="ph ph-x-circle f-18"></i>
                    <span>${message}</span>
                </div>
            `;
            break;
    }
}

// Update check status
function updateCheckStatus(checkId, status, message) {
    const checkEl = document.getElementById(`check-${checkId}`);
    const iconEl = document.getElementById(`icon-${checkId}`);
    const statusTextEl = document.getElementById(`status-${checkId}`);
    
    // Reset classes
    checkEl.className = 'check-item m-3';
    iconEl.className = 'status-icon';
    
    // Add status class
    checkEl.classList.add(status);
    iconEl.classList.add(status);
    
    // Update message
    statusTextEl.textContent = message;
    
    // Add margin bottom for non-last items
    if (checkId !== 'fullscreen') {
        checkEl.classList.add('mb-0');
    }
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
        checks.fullscreen = true; // Still allow to proceed
    }
    
    validateAllChecks();
}

// Validate all checks
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
        
        formIcon.innerHTML = '<i class="ph ph-lock-open f-32 text-success"></i>';
        formIcon.classList.remove('bg-danger', 'bg-opacity-10');
        formIcon.classList.add('bg-success', 'bg-opacity-10');
        
        formStatusText.textContent = 'Semua pemeriksaan selesai. Silakan masukkan token ujian.';
        formStatusText.classList.remove('text-muted');
        formStatusText.classList.add('text-success');
        
        tokenInput.disabled = false;
        agreeCheckbox.disabled = false;
        
        // Enable submit button when agreement is checked
        agreeCheckbox.addEventListener('change', function() {
            if (this.checked && tokenInput.value.trim().length > 0) {
                startBtn.disabled = false;
                startBtnText.textContent = 'Mulai Ujian';
                startBtn.innerHTML = '<i class="ph ph-play me-2"></i><span>' + startBtnText.textContent + '</span>';
            } else {
                startBtn.disabled = true;
            }
        });
        
        tokenInput.addEventListener('input', function() {
            if (agreeCheckbox.checked && this.value.trim().length > 0) {
                startBtn.disabled = false;
                startBtnText.textContent = 'Mulai Ujian';
                startBtn.innerHTML = '<i class="ph ph-play me-2"></i><span>' + startBtnText.textContent + '</span>';
            } else {
                startBtn.disabled = true;
            }
        });
        
        cameraVerifiedInput.value = '1';
        
    } else {
        formContainer.classList.add('not-ready');
        formContainer.classList.remove('ready');
        
        formIcon.innerHTML = '<i class="ph ph-lock f-32 text-danger"></i>';
        formIcon.classList.add('bg-danger', 'bg-opacity-10');
        formIcon.classList.remove('bg-success', 'bg-opacity-10');
        
        // Show which checks are failing
        const failingChecks = [];
        if (!checks.camera) failingChecks.push('Kamera');
        if (!checks.face) failingChecks.push('Deteksi Wajah');
        if (!checks.browser) failingChecks.push('Browser');
        if (!checks.fullscreen) failingChecks.push('Fullscreen');
        
        if (failingChecks.length > 0) {
            formStatusText.textContent = `Perlu diselesaikan: ${failingChecks.join(', ')}`;
        } else {
            formStatusText.textContent = 'Lengkapi semua pemeriksaan di atas terlebih dahulu';
        }
        formStatusText.classList.add('text-muted');
        formStatusText.classList.remove('text-success');
        
        tokenInput.disabled = true;
        agreeCheckbox.disabled = true;
        startBtn.disabled = true;
        startBtnText.textContent = 'Selesaikan Pemeriksaan Terlebih Dahulu';
        
        cameraVerifiedInput.value = '0';
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (faceDetectionInterval) {
        clearInterval(faceDetectionInterval);
    }
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endsection
