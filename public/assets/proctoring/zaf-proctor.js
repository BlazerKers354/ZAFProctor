/**
 * ZAFProctor - Advanced Proctoring System
 * Modern face detection and exam monitoring
 */

class ZAFProctor {
    constructor(config) {
        this.config = {
            attemptId: null,
            examId: null,
            csrfToken: '',
            endpoints: {
                logViolation: '',
                uploadSnapshot: '',
                heartbeat: '',
                autoSubmit: '',
            },
            modelPath: '/assets/proctoring/models',
            
            // Detection settings
            requireCamera: true,
            requireFullscreen: true,
            detectFace: true,
            detectMultipleFaces: true,
            detectTabSwitch: true,
            detectCopyPaste: true,
            detectRightClick: true,
            blockKeyboardShortcuts: true,
            
            // Thresholds
            maxViolations: 5,
            warningThreshold: 3,
            snapshotInterval: 30, // seconds
            faceDetectionInterval: 2000, // ms
            heartbeatInterval: 30000, // ms
            noFaceWarningDelay: 5000, // ms before logging no face violation
            
            // Callbacks
            onViolation: null,
            onAutoSubmit: null,
            onWarning: null,
            onFaceDetected: null,
            onFaceLost: null,
            
            ...config
        };
        
        this.state = {
            stream: null,
            violationCount: 0,
            faceDetected: false,
            faceDetectionActive: false,
            lastFaceDetectedTime: null,
            noFaceTimeout: null,
            isSubmitting: false,
            isPaused: false,
        };
        
        this.intervals = {
            faceDetection: null,
            snapshot: null,
            heartbeat: null,
        };
        
        this.elements = {
            video: null,
            canvas: null,
            statusIndicator: null,
        };
    }
    
    /**
     * Initialize the proctoring system
     */
    async init() {
        console.log('[ZAFProctor] Initializing...');
        
        // Load face-api models if face detection is enabled
        if (this.config.detectFace) {
            await this.loadModels();
        }
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Initialize camera if required
        if (this.config.requireCamera) {
            await this.initCamera();
        }
        
        // Start heartbeat
        this.startHeartbeat();
        
        console.log('[ZAFProctor] Initialized successfully');
        
        return true;
    }
    
    /**
     * Load face-api.js models
     */
    async loadModels() {
        try {
            console.log('[ZAFProctor] Loading face detection models...');
            
            // Load TinyFaceDetector model (lightweight and fast)
            await faceapi.nets.tinyFaceDetector.loadFromUri(this.config.modelPath);
            
            console.log('[ZAFProctor] Face detection models loaded');
            return true;
        } catch (error) {
            console.error('[ZAFProctor] Error loading models:', error);
            return false;
        }
    }
    
    /**
     * Initialize camera stream
     */
    async initCamera() {
        try {
            console.log('[ZAFProctor] Requesting camera access...');
            
            this.state.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 320 },
                    height: { ideal: 240 },
                    facingMode: 'user'
                },
                audio: false
            });
            
            // Setup video element
            this.elements.video = document.getElementById('camera-preview');
            if (this.elements.video) {
                this.elements.video.srcObject = this.state.stream;
                
                this.elements.video.onloadedmetadata = () => {
                    this.elements.video.play();
                    this.setupFaceCanvas();
                    
                    // Start face detection
                    if (this.config.detectFace) {
                        this.startFaceDetection();
                    }
                    
                    // Start snapshot capture
                    this.startSnapshotCapture();
                };
            }
            
            this.updateCameraStatus(true);
            console.log('[ZAFProctor] Camera initialized');
            
            return true;
        } catch (error) {
            console.error('[ZAFProctor] Camera error:', error);
            this.updateCameraStatus(false);
            this.logViolation('camera_disabled', 'Camera access denied or unavailable');
            return false;
        }
    }
    
    /**
     * Setup face detection canvas
     */
    setupFaceCanvas() {
        this.elements.canvas = document.getElementById('face-canvas');
        if (this.elements.canvas && this.elements.video) {
            this.elements.canvas.width = this.elements.video.videoWidth || 320;
            this.elements.canvas.height = this.elements.video.videoHeight || 240;
        }
    }
    
    /**
     * Start face detection loop
     */
    startFaceDetection() {
        if (this.state.faceDetectionActive) return;
        
        console.log('[ZAFProctor] Starting face detection...');
        this.state.faceDetectionActive = true;
        
        this.intervals.faceDetection = setInterval(async () => {
            if (this.state.isPaused || this.state.isSubmitting) return;
            
            await this.detectFace();
        }, this.config.faceDetectionInterval);
    }
    
    /**
     * Perform face detection
     */
    async detectFace() {
        if (!this.elements.video || !this.elements.video.videoWidth) return;
        
        try {
            const detections = await faceapi.detectAllFaces(
                this.elements.video,
                new faceapi.TinyFaceDetectorOptions({
                    inputSize: 224,
                    scoreThreshold: 0.5
                })
            );
            
            // Clear canvas
            if (this.elements.canvas) {
                const ctx = this.elements.canvas.getContext('2d');
                ctx.clearRect(0, 0, this.elements.canvas.width, this.elements.canvas.height);
            }
            
            if (detections.length === 0) {
                // No face detected
                this.handleNoFace();
            } else if (detections.length === 1) {
                // One face - good
                this.handleFaceDetected(detections[0]);
            } else {
                // Multiple faces - violation
                this.handleMultipleFaces(detections);
            }
            
        } catch (error) {
            console.error('[ZAFProctor] Face detection error:', error);
        }
    }
    
    /**
     * Handle no face detected
     */
    handleNoFace() {
        if (this.state.faceDetected) {
            this.state.faceDetected = false;
            this.updateFaceStatus(false, 'Wajah tidak terdeteksi');
            
            if (this.config.onFaceLost) {
                this.config.onFaceLost();
            }
            
            // Start countdown for violation
            if (!this.state.noFaceTimeout) {
                this.state.noFaceTimeout = setTimeout(() => {
                    if (!this.state.faceDetected && !this.state.isSubmitting) {
                        this.logViolation('no_face_detected', 'Face not detected for extended period');
                        this.captureAndUploadSnapshot('no_face_detected', 'No face detected');
                    }
                }, this.config.noFaceWarningDelay);
            }
        }
    }
    
    /**
     * Handle single face detected
     */
    handleFaceDetected(detection) {
        // Clear no face timeout
        if (this.state.noFaceTimeout) {
            clearTimeout(this.state.noFaceTimeout);
            this.state.noFaceTimeout = null;
        }
        
        this.state.faceDetected = true;
        this.state.lastFaceDetectedTime = Date.now();
        
        // Draw face box on canvas
        if (this.elements.canvas) {
            const ctx = this.elements.canvas.getContext('2d');
            const box = detection.box;
            
            ctx.strokeStyle = '#22c55e';
            ctx.lineWidth = 2;
            ctx.strokeRect(box.x, box.y, box.width, box.height);
        }
        
        this.updateFaceStatus(true, `Wajah terdeteksi (${Math.round(detection.score * 100)}%)`);
        
        if (this.config.onFaceDetected) {
            this.config.onFaceDetected(detection);
        }
    }
    
    /**
     * Handle multiple faces detected
     */
    handleMultipleFaces(detections) {
        console.log('[ZAFProctor] Multiple faces detected:', detections.length);
        
        this.updateFaceStatus(false, `${detections.length} wajah terdeteksi!`);
        
        if (this.config.detectMultipleFaces) {
            this.logViolation('multiple_faces', `${detections.length} faces detected`);
            this.captureAndUploadSnapshot('multiple_faces', `${detections.length} faces detected`);
        }
        
        // Draw all faces in red
        if (this.elements.canvas) {
            const ctx = this.elements.canvas.getContext('2d');
            detections.forEach(detection => {
                const box = detection.box;
                ctx.strokeStyle = '#ef4444';
                ctx.lineWidth = 2;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
            });
        }
    }
    
    /**
     * Update camera status indicator
     */
    updateCameraStatus(isActive) {
        const statusEl = document.getElementById('camera-status');
        if (statusEl) {
            if (isActive) {
                statusEl.classList.remove('bg-red-500');
                statusEl.classList.add('bg-green-500');
            } else {
                statusEl.classList.remove('bg-green-500');
                statusEl.classList.add('bg-red-500');
            }
        }
    }
    
    /**
     * Update face detection status
     */
    updateFaceStatus(isDetected, message) {
        // Update any face status elements in the UI
        const faceIndicator = document.getElementById('face-indicator');
        if (faceIndicator) {
            faceIndicator.textContent = message;
            faceIndicator.className = isDetected ? 'text-green-400' : 'text-red-400';
        }
    }
    
    /**
     * Setup event listeners for proctoring
     */
    setupEventListeners() {
        // Tab visibility change
        if (this.config.detectTabSwitch) {
            document.addEventListener('visibilitychange', () => {
                if (document.hidden && !this.state.isSubmitting) {
                    this.logViolation('tab_switch', 'User switched to another tab');
                }
            });
        }
        
        // Window blur
        window.addEventListener('blur', () => {
            if (!this.state.isSubmitting) {
                this.logViolation('window_blur', 'Window lost focus');
            }
        });
        
        // Copy/paste prevention
        if (this.config.detectCopyPaste) {
            ['copy', 'cut', 'paste'].forEach(event => {
                document.addEventListener(event, (e) => {
                    if (!this.state.isSubmitting) {
                        e.preventDefault();
                        this.logViolation('copy_paste', `${event} action detected`);
                    }
                });
            });
        }
        
        // Right click prevention
        if (this.config.detectRightClick) {
            document.addEventListener('contextmenu', (e) => {
                if (!this.state.isSubmitting) {
                    e.preventDefault();
                    this.logViolation('right_click', 'Right click detected');
                }
            });
        }
        
        // Keyboard shortcuts
        if (this.config.blockKeyboardShortcuts) {
            document.addEventListener('keydown', (e) => {
                if (this.state.isSubmitting) return;
                
                // Block common shortcuts
                const blockedShortcuts = ['c', 'v', 'x', 'a', 'p', 's', 'f', 'u'];
                if ((e.ctrlKey || e.metaKey) && blockedShortcuts.includes(e.key.toLowerCase())) {
                    e.preventDefault();
                    this.logViolation('keyboard_shortcut', `Blocked: ${e.ctrlKey ? 'Ctrl' : 'Cmd'}+${e.key}`);
                }
                
                // Block F12 (DevTools)
                if (e.key === 'F12') {
                    e.preventDefault();
                    this.logViolation('keyboard_shortcut', 'Blocked F12 key');
                }
                
                // Block F5 (Refresh)
                if (e.key === 'F5') {
                    e.preventDefault();
                    this.logViolation('browser_refresh', 'Refresh attempt blocked');
                }
            });
        }
        
        // Fullscreen change
        if (this.config.requireFullscreen) {
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement && !this.state.isSubmitting) {
                    this.logViolation('fullscreen_exit', 'User exited fullscreen mode');
                }
            });
        }
        
        // Prevent before unload
        window.addEventListener('beforeunload', (e) => {
            if (!this.state.isSubmitting) {
                e.preventDefault();
                e.returnValue = 'Ujian sedang berlangsung. Yakin ingin meninggalkan halaman?';
                return e.returnValue;
            }
        });
    }
    
    /**
     * Start periodic snapshot capture
     */
    startSnapshotCapture() {
        if (this.config.snapshotInterval <= 0) return;
        
        console.log(`[ZAFProctor] Starting snapshot capture every ${this.config.snapshotInterval}s`);
        
        this.intervals.snapshot = setInterval(() => {
            if (!this.state.isSubmitting) {
                this.captureAndUploadSnapshot();
            }
        }, this.config.snapshotInterval * 1000);
    }
    
    /**
     * Capture and upload snapshot
     */
    async captureAndUploadSnapshot(violationType = null, description = null) {
        if (!this.elements.video || !this.state.stream) return;
        
        try {
            const canvas = document.createElement('canvas');
            canvas.width = this.elements.video.videoWidth || 320;
            canvas.height = this.elements.video.videoHeight || 240;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this.elements.video, 0, 0, canvas.width, canvas.height);
            
            // Add timestamp overlay
            ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            ctx.fillRect(0, canvas.height - 25, canvas.width, 25);
            ctx.fillStyle = '#fff';
            ctx.font = '12px Arial';
            ctx.fillText(new Date().toLocaleString('id-ID'), 5, canvas.height - 8);
            
            const imageData = canvas.toDataURL('image/jpeg', 0.7);
            
            await fetch(this.config.endpoints.uploadSnapshot, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                },
                body: JSON.stringify({
                    snapshot: imageData,
                    violation_type: violationType,
                    description: description
                })
            });
            
            console.log('[ZAFProctor] Snapshot uploaded');
            
        } catch (error) {
            console.error('[ZAFProctor] Snapshot upload error:', error);
        }
    }
    
    /**
     * Log a violation
     */
    async logViolation(type, description) {
        if (this.state.isSubmitting) return;
        
        console.log(`[ZAFProctor] Violation: ${type} - ${description}`);
        
        this.state.violationCount++;
        
        // Show warning
        this.showWarning(description);
        
        // Update UI
        this.updateViolationCounter();
        
        // Callback
        if (this.config.onViolation) {
            this.config.onViolation(type, description, this.state.violationCount);
        }
        
        try {
            const response = await fetch(this.config.endpoints.logViolation, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                },
                body: JSON.stringify({
                    violation_type: type,
                    description: description
                })
            });
            
            const data = await response.json();
            
            // Check if should auto-submit
            if (data.should_auto_submit) {
                this.triggerAutoSubmit();
            }
            
        } catch (error) {
            console.error('[ZAFProctor] Error logging violation:', error);
        }
    }
    
    /**
     * Show warning to user
     */
    showWarning(message) {
        const banner = document.getElementById('warning-banner');
        const msgEl = document.getElementById('warning-message');
        
        if (banner && msgEl) {
            msgEl.textContent = `⚠️ Peringatan: ${message} (${this.state.violationCount}/${this.config.maxViolations})`;
            banner.classList.remove('hidden');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                banner.classList.add('hidden');
            }, 5000);
        }
        
        // Callback
        if (this.config.onWarning) {
            this.config.onWarning(message, this.state.violationCount);
        }
    }
    
    /**
     * Update violation counter in UI
     */
    updateViolationCounter() {
        const counter = document.getElementById('violation-counter');
        const countEl = document.getElementById('violation-count');
        
        if (counter && countEl) {
            counter.style.display = 'flex';
            countEl.textContent = this.state.violationCount;
            
            if (this.state.violationCount >= this.config.warningThreshold) {
                counter.classList.add('animate-pulse');
            }
        }
    }
    
    /**
     * Start heartbeat
     */
    startHeartbeat() {
        this.intervals.heartbeat = setInterval(async () => {
            if (this.state.isSubmitting) return;
            
            try {
                const response = await fetch(this.config.endpoints.heartbeat, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.config.csrfToken,
                    },
                    body: JSON.stringify({
                        camera_enabled: this.state.stream !== null,
                        face_detected: this.state.faceDetected,
                        violation_count: this.state.violationCount
                    })
                });
                
                const data = await response.json();
                
                if (data.should_submit) {
                    this.triggerAutoSubmit();
                }
                
            } catch (error) {
                console.error('[ZAFProctor] Heartbeat error:', error);
            }
        }, this.config.heartbeatInterval);
    }
    
    /**
     * Trigger auto submit
     */
    async triggerAutoSubmit() {
        if (this.state.isSubmitting) return;
        
        this.state.isSubmitting = true;
        console.log('[ZAFProctor] Triggering auto-submit...');
        
        // Callback
        if (this.config.onAutoSubmit) {
            this.config.onAutoSubmit();
        }
        
        try {
            const response = await fetch(this.config.endpoints.autoSubmit, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                }
            });
            
            const data = await response.json();
            
            if (data.redirect) {
                window.location.href = data.redirect;
            }
            
        } catch (error) {
            console.error('[ZAFProctor] Auto-submit error:', error);
            this.state.isSubmitting = false;
        }
    }
    
    /**
     * Mark as submitting (call before manual submit)
     */
    setSubmitting(value) {
        this.state.isSubmitting = value;
    }
    
    /**
     * Pause proctoring temporarily
     */
    pause() {
        this.state.isPaused = true;
    }
    
    /**
     * Resume proctoring
     */
    resume() {
        this.state.isPaused = false;
    }
    
    /**
     * Stop all proctoring
     */
    stop() {
        console.log('[ZAFProctor] Stopping...');
        
        // Clear all intervals
        Object.values(this.intervals).forEach(interval => {
            if (interval) clearInterval(interval);
        });
        
        // Stop camera stream
        if (this.state.stream) {
            this.state.stream.getTracks().forEach(track => track.stop());
            this.state.stream = null;
        }
        
        // Clear timeout
        if (this.state.noFaceTimeout) {
            clearTimeout(this.state.noFaceTimeout);
        }
        
        console.log('[ZAFProctor] Stopped');
    }
    
    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }
}

// Export for use
window.ZAFProctor = ZAFProctor;
