@extends('layouts.exam')

@section('content')
    <div id="exam-app" class="min-h-screen flex flex-col">
        <!-- Top Bar -->
        <div class="sticky top-0 z-50 shadow-lg" style="background: var(--exam-header-bg);">
            <div class="px-4 lg:px-6 py-3">
                <div class="flex items-center justify-between">
                    <!-- Left: Exam Info -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                            <i class="bi bi-journal-text text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-white font-semibold text-lg leading-tight">{{ $attempt->exam->title }}</h1>
                            <div class="flex items-center space-x-2 text-gray-400 text-sm">
                                <i class="bi bi-book"></i>
                                <span>{{ $attempt->exam->course->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 lg:space-x-6">
                        <!-- Camera Preview -->
                        <div class="camera-container hidden sm:block">
                            <video id="camera-preview" class="w-20 h-14 lg:w-24 lg:h-18 object-cover" autoplay muted playsinline></video>
                            <div id="camera-status" class="absolute bottom-1 right-1 w-3 h-3 rounded-full bg-green-500 border-2 border-white shadow-sm">
                                <span class="absolute inset-0 rounded-full bg-green-400 animate-ping opacity-75"></span>
                            </div>
                        </div>
                        
                        <!-- Timer -->
                        <div id="timer" class="flex items-center space-x-3 bg-gradient-to-r from-slate-700 to-slate-800 px-4 py-2.5 rounded-xl border border-slate-600 shadow-lg">
                            <div class="flex items-center justify-center w-8 h-8 bg-amber-500/20 rounded-lg">
                                <i class="bi bi-clock-fill text-amber-400"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-400 font-medium">Sisa Waktu</div>
                                <span id="timer-display" class="font-mono-timer text-xl font-bold text-white tracking-wider">--:--</span>
                            </div>
                        </div>
                        
                        <!-- Violation Counter -->
                        <div id="violation-counter" class="items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 px-3 py-2 rounded-xl shadow-lg border border-red-500" style="display: none;">
                            <div class="flex items-center justify-center w-7 h-7 bg-white/20 rounded-lg">
                                <i class="bi bi-exclamation-triangle-fill text-white"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-red-200">Pelanggaran</div>
                                <span id="violation-count" class="font-bold text-white text-lg">0</span>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button onclick="confirmSubmit()" 
                                class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 px-5 py-2.5 rounded-xl font-medium text-white transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5 border border-indigo-500">
                            <i class="bi bi-send-fill"></i>
                            <span>Kumpulkan</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="h-1 bg-slate-700">
                <div id="progress-bar" class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>
        
        <!-- Warning Banner -->
        <div id="warning-banner" class="bg-gradient-to-r from-red-600 to-red-700 text-white text-center py-3 hidden shadow-lg">
            <div class="flex items-center justify-center space-x-2">
                <i class="bi bi-exclamation-octagon-fill text-lg"></i>
                <span id="warning-message" class="font-medium">Peringatan!</span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex">
            <!-- Question Navigation Sidebar -->
            <div class="w-64 lg:w-72 p-5 overflow-y-auto border-r border-slate-200" style="background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);">
                <div class="mb-5">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-1">Navigasi Soal</h3>
                    <p class="text-slate-400 text-xs">Klik nomor untuk berpindah soal</p>
                </div>
                
                <div class="grid grid-cols-5 gap-2 mb-6" id="question-nav">
                    @foreach($questions as $index => $question)
                        <button onclick="goToQuestion({{ $index }})"
                                id="nav-btn-{{ $index }}"
                                class="question-nav-btn w-10 h-10 rounded-lg text-sm font-semibold transition-all duration-200
                                       {{ isset($answeredQuestions[$question->id]) ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                
                <!-- Legend -->
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
                    <h4 class="text-white text-xs font-semibold uppercase tracking-wider mb-3">Keterangan</h4>
                    <div class="space-y-2.5">
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-md shadow"></div>
                            <span class="text-slate-300 text-sm">Sudah dijawab</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-slate-700 rounded-md"></div>
                            <span class="text-slate-300 text-sm">Belum dijawab</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-slate-700 rounded-md ring-2 ring-white"></div>
                            <span class="text-slate-300 text-sm">Soal saat ini</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="mt-5 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-4 shadow-lg">
                    <h4 class="text-white/80 text-xs font-semibold uppercase tracking-wider mb-3">Progress</h4>
                    <div class="flex items-end justify-between">
                        <div>
                            <span id="answered-count" class="text-3xl font-bold text-white">{{ count($answeredQuestions) }}</span>
                            <span class="text-white/70 text-lg">/{{ $questions->count() }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-white/60 text-xs">Terjawab</div>
                            <div id="answered-percent" class="text-white font-semibold">{{ $questions->count() > 0 ? round((count($answeredQuestions) / $questions->count()) * 100) : 0 }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Submit Button -->
                <button onclick="confirmSubmit()" 
                        class="sm:hidden w-full mt-5 flex items-center justify-center space-x-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 px-5 py-3 rounded-xl font-medium text-white transition-all duration-200 shadow-lg">
                    <i class="bi bi-send-fill"></i>
                    <span>Kumpulkan Ujian</span>
                </button>
            </div>
            
            <!-- Question Content -->
            <div class="flex-1 p-6 lg:p-8 overflow-y-auto" style="background: var(--exam-body-bg);">
                <div id="questions-container" class="max-w-4xl mx-auto">
                    @foreach($questions as $index => $question)
                        <div id="question-{{ $index }}" 
                             class="question-panel fade-in {{ $index === 0 ? '' : 'hidden' }}">
                            
                            <!-- Question Card -->
                            <div class="glass-effect rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                                <!-- Question Header -->
                                <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-white/10 rounded-xl">
                                                <span class="text-white font-bold">{{ $index + 1 }}</span>
                                            </div>
                                            <div>
                                                <span class="text-white font-medium">Soal {{ $index + 1 }}</span>
                                                <span class="text-slate-400 text-sm"> dari {{ $questions->count() }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 bg-indigo-500/20 px-3 py-1.5 rounded-lg">
                                            <i class="bi bi-star-fill text-indigo-400 text-sm"></i>
                                            <span class="text-indigo-300 font-semibold text-sm">{{ $question->points }} poin</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 lg:p-8">
                                    <!-- Question Text -->
                                    <div class="mb-8">
                                        <p class="text-lg lg:text-xl text-slate-800 leading-relaxed">{!! nl2br(e($question->question)) !!}</p>
                                        
                                        @if($question->question_image)
                                            <div class="mt-6">
                                                <img src="{{ asset('storage/' . $question->question_image) }}" 
                                                     alt="Question Image" 
                                                     class="max-w-full lg:max-w-2xl rounded-xl shadow-lg border border-slate-200">
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Answer Options -->
                                    @if($question->isMultipleChoice())
                                        <div class="space-y-3">
                                            @foreach($question->options as $option)
                                                <label class="option-card flex items-start p-4 lg:p-5 bg-white border-2 border-slate-200 rounded-xl cursor-pointer
                                                              {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'selected border-indigo-500 bg-indigo-50' : '' }}"
                                                       onclick="selectOption({{ $question->id }}, {{ $option->id }}, {{ $index }})"
                                                       id="option-{{ $question->id }}-{{ $option->id }}">
                                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg border-2 border-slate-300 mr-4 flex-shrink-0
                                                                {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'bg-indigo-600 border-indigo-600' : 'bg-white' }}">
                                                        @if(isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id)
                                                            <i class="bi bi-check text-white text-lg"></i>
                                                        @else
                                                            <span class="text-slate-500 font-semibold text-sm">{{ $option->option_label }}</span>
                                                        @endif
                                                    </div>
                                                    <input type="radio" 
                                                           name="question_{{ $question->id }}" 
                                                           value="{{ $option->id }}"
                                                           class="hidden"
                                                           {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}>
                                                    <div class="flex-1">
                                                        <span class="text-slate-800 text-base lg:text-lg">{{ $option->option_text }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- Essay Answer -->
                                        <div>
                                            <label class="flex items-center space-x-2 text-sm font-semibold text-slate-700 mb-3">
                                                <i class="bi bi-pencil-square text-indigo-600"></i>
                                                <span>Jawaban Anda:</span>
                                            </label>
                                            <textarea id="essay-{{ $question->id }}"
                                                      rows="10"
                                                      class="w-full border-2 border-slate-200 rounded-xl p-5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-slate-800 text-base resize-none"
                                                      placeholder="Tulis jawaban Anda di sini..."
                                                      onblur="saveEssayAnswer({{ $question->id }}, {{ $index }})"
                                            >{{ isset($answeredQuestions[$question->id]) ? $answeredQuestions[$question->id]->essay_answer : '' }}</textarea>
                                            <div class="flex items-center justify-between mt-2 text-xs text-slate-500">
                                                <span><i class="bi bi-info-circle mr-1"></i>Jawaban otomatis tersimpan saat Anda berpindah soal</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Navigation Buttons -->
                                <div class="bg-slate-50 px-6 lg:px-8 py-4 border-t border-slate-200">
                                    <div class="flex justify-between items-center">
                                        <button onclick="goToQuestion({{ $index - 1 }})" 
                                                class="flex items-center space-x-2 px-5 py-2.5 border-2 border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 hover:border-slate-400 transition-all duration-200
                                                       {{ $index === 0 ? 'invisible' : '' }}">
                                            <i class="bi bi-arrow-left"></i>
                                            <span class="font-medium">Sebelumnya</span>
                                        </button>
                                        
                                        @if($index === $questions->count() - 1)
                                            <button onclick="confirmSubmit()" 
                                                    class="flex items-center space-x-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl font-medium">
                                                <span>Kumpulkan Ujian</span>
                                                <i class="bi bi-send-fill"></i>
                                            </button>
                                        @else
                                            <button onclick="goToQuestion({{ $index + 1 }})" 
                                                    class="flex items-center space-x-2 px-5 py-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition-all duration-200 shadow-lg font-medium">
                                                <span>Selanjutnya</span>
                                                <i class="bi bi-arrow-right"></i>
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

    <!-- Submit Confirmation Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden fade-in">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-send-check text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Konfirmasi Pengumpulan</h3>
            </div>
            <div class="p-6">
                <div id="submit-summary" class="mb-6">
                    <!-- Will be filled by JS -->
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeSubmitModal()" 
                            class="flex-1 flex items-center justify-center space-x-2 px-4 py-3 border-2 border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 transition-all duration-200 font-medium">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali</span>
                    </button>
                    <form id="submit-form" action="{{ route('student.exams.submit', $attempt) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 font-medium shadow-lg">
                            <span>Ya, Kumpulkan</span>
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Warning Modal -->
    <div id="fullscreen-modal" class="fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden fade-in">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-8 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-fullscreen text-white text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Mode Fullscreen Diperlukan</h3>
                <p class="text-white/80">
                    Ujian ini memerlukan mode fullscreen untuk mencegah kecurangan.
                </p>
            </div>
            <div class="p-6 text-center">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="bi bi-info-circle-fill text-amber-600 text-lg mt-0.5"></i>
                        <p class="text-amber-800 text-sm text-left">
                            Keluar dari mode fullscreen akan dicatat sebagai pelanggaran dan dapat mengakibatkan ujian Anda dibatalkan.
                        </p>
                    </div>
                </div>
                <button onclick="enterFullscreen()" 
                        class="w-full flex items-center justify-center space-x-2 px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="bi bi-fullscreen"></i>
                    <span>Aktifkan Fullscreen</span>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Configuration from server
    const config = {
        attemptId: {{ $attempt->id }},
        examId: {{ $attempt->exam->id }},
        totalQuestions: {{ $questions->count() }},
        remainingTime: {{ $attempt->remaining_time }},
        requireCamera: {{ $attempt->exam->settings?->webcam_enabled ? 'true' : 'false' }},
        requireFullscreen: {{ $attempt->exam->settings?->browser_lock_enabled ? 'true' : 'false' }},
        snapshotInterval: {{ $attempt->exam->settings?->snapshot_interval ?? 30 }},
        maxViolations: {{ $attempt->exam->settings?->max_tab_switches ?? 5 }},
        warningThreshold: {{ $attempt->exam->settings?->warning_threshold ?? 3 }},
        csrfToken: '{{ csrf_token() }}',
        endpoints: {
            saveAnswer: '{{ route("student.exams.save-answer", $attempt) }}',
            logViolation: '{{ route("student.proctoring.violation", $attempt) }}',
            uploadSnapshot: '{{ route("student.proctoring.snapshot", $attempt) }}',
            heartbeat: '{{ route("student.proctoring.heartbeat", $attempt) }}',
            autoSubmit: '{{ route("student.exams.auto-submit", $attempt) }}',
            syncTime: '{{ route("student.exams.sync-time", $attempt) }}',
        }
    };

    let currentQuestion = 0;
    let violationCount = {{ $attempt->violation_count }};
    let answeredQuestions = new Set([
        @foreach($answeredQuestions as $questionId => $answer)
            {{ $questionId }},
        @endforeach
    ]);
    let stream = null;
    let snapshotInterval = null;
    let timerInterval = null;
    let heartbeatInterval = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initTimer();
        initCamera();
        initProctoring();
        initFullscreen();
        startHeartbeat();
        updateProgressBar();
        highlightCurrentQuestion(0);
        
        // Prevent copy/paste
        document.addEventListener('copy', preventCopyPaste);
        document.addEventListener('cut', preventCopyPaste);
        document.addEventListener('paste', preventCopyPaste);
        
        // Prevent right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            logViolation('right_click', 'Right click detected');
        });
        
        // Prevent keyboard shortcuts
        document.addEventListener('keydown', preventKeyboardShortcuts);
    });

    // Update progress bar
    function updateProgressBar() {
        const progress = (answeredQuestions.size / config.totalQuestions) * 100;
        document.getElementById('progress-bar').style.width = progress + '%';
        
        // Update stats
        const countEl = document.getElementById('answered-count');
        const percentEl = document.getElementById('answered-percent');
        if (countEl) countEl.textContent = answeredQuestions.size;
        if (percentEl) percentEl.textContent = Math.round(progress) + '%';
    }

    // Highlight current question in nav
    function highlightCurrentQuestion(index) {
        document.querySelectorAll('#question-nav button').forEach((btn, i) => {
            btn.classList.remove('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-800');
            if (i === index) {
                btn.classList.add('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-800');
            }
        });
    }

    // Timer
    function initTimer() {
        let timeRemaining = config.remainingTime;
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
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
            
            document.getElementById('timer-display').textContent = display;
            
            // Warning when less than 5 minutes
            if (timeRemaining <= 300) {
                const timerEl = document.getElementById('timer');
                timerEl.classList.add('timer-warning');
                timerEl.style.background = 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)';
                timerEl.style.borderColor = '#ef4444';
            }
            
            timeRemaining--;
        }
        
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Camera
    async function initCamera() {
        if (!config.requireCamera) return;
        
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 320, height: 240, facingMode: 'user' },
                audio: false 
            });
            
            const video = document.getElementById('camera-preview');
            video.srcObject = stream;
            
            document.getElementById('camera-status').classList.remove('bg-red-500');
            document.getElementById('camera-status').classList.add('bg-green-500');
            
            // Start snapshot capture
            startSnapshotCapture();
            
        } catch (error) {
            console.error('Camera error:', error);
            document.getElementById('camera-status').classList.remove('bg-green-500');
            document.getElementById('camera-status').classList.add('bg-red-500');
            logViolation('camera_disabled', 'Camera access denied or not available');
        }
    }

    // Snapshot capture
    function startSnapshotCapture() {
        snapshotInterval = setInterval(captureAndUploadSnapshot, config.snapshotInterval * 1000);
    }

    function captureAndUploadSnapshot(violationType = null, description = null) {
        const video = document.getElementById('camera-preview');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 320;
        canvas.height = video.videoHeight || 240;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        
        fetch(config.endpoints.uploadSnapshot, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
            body: JSON.stringify({
                snapshot: imageData,
                violation_type: violationType,
                description: description
            })
        }).catch(err => console.error('Snapshot upload error:', err));
    }

    // Proctoring
    function initProctoring() {
        // Tab visibility
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                logViolation('tab_switch', 'User switched to another tab');
            }
        });
        
        // Window blur
        window.addEventListener('blur', function() {
            logViolation('window_blur', 'Window lost focus');
        });
    }

    // Fullscreen
    function initFullscreen() {
        if (!config.requireFullscreen) return;
        
        if (!document.fullscreenElement) {
            document.getElementById('fullscreen-modal').classList.remove('hidden');
        }
        
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && config.requireFullscreen) {
                document.getElementById('fullscreen-modal').classList.remove('hidden');
                logViolation('fullscreen_exit', 'User exited fullscreen mode');
            } else {
                document.getElementById('fullscreen-modal').classList.add('hidden');
            }
        });
    }

    function enterFullscreen() {
        document.documentElement.requestFullscreen().then(() => {
            document.getElementById('fullscreen-modal').classList.add('hidden');
        }).catch(err => {
            console.error('Fullscreen error:', err);
        });
    }

    // Log violation
    async function logViolation(type, description) {
        violationCount++;
        updateViolationCounter();
        showWarning(description);
        
        // Capture snapshot with violation
        if (stream) {
            captureAndUploadSnapshot(type, description);
        }
        
        try {
            const response = await fetch(config.endpoints.logViolation, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({
                    violation_type: type,
                    description: description
                })
            });
            
            const data = await response.json();
            
            if (data.should_auto_submit) {
                autoSubmit();
            }
        } catch (err) {
            console.error('Error logging violation:', err);
        }
    }

    function updateViolationCounter() {
        const counter = document.getElementById('violation-counter');
        const countEl = document.getElementById('violation-count');
        
        counter.style.display = 'flex';
        countEl.textContent = violationCount;
        
        if (violationCount >= config.warningThreshold) {
            counter.classList.add('animate-pulse');
        }
    }

    function showWarning(message) {
        const banner = document.getElementById('warning-banner');
        const msgEl = document.getElementById('warning-message');
        
        msgEl.textContent = `⚠️ Peringatan: ${message} (${violationCount}/${config.maxViolations})`;
        banner.classList.remove('hidden');
        
        setTimeout(() => {
            banner.classList.add('hidden');
        }, 5000);
    }

    // Heartbeat
    function startHeartbeat() {
        heartbeatInterval = setInterval(async () => {
            try {
                const response = await fetch(config.endpoints.heartbeat, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify({
                        camera_enabled: stream !== null
                    })
                });
                
                const data = await response.json();
                
                if (data.should_submit) {
                    autoSubmit();
                }
            } catch (err) {
                console.error('Heartbeat error:', err);
            }
        }, 30000); // Every 30 seconds
    }

    // Prevent copy/paste
    function preventCopyPaste(e) {
        e.preventDefault();
        logViolation('copy_paste', 'Copy/paste action detected');
    }

    // Prevent keyboard shortcuts
    function preventKeyboardShortcuts(e) {
        // Ctrl/Cmd + C, V, X, A, P, S, F, Tab, Alt+Tab
        if ((e.ctrlKey || e.metaKey) && ['c', 'v', 'x', 'a', 'p', 's', 'f'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            logViolation('keyboard_shortcut', `Blocked shortcut: ${e.ctrlKey ? 'Ctrl' : 'Cmd'}+${e.key}`);
        }
        
        // F12 (DevTools)
        if (e.key === 'F12') {
            e.preventDefault();
            logViolation('keyboard_shortcut', 'Blocked F12 key');
        }
        
        // Alt+Tab (can't fully prevent but can detect blur)
        if (e.altKey && e.key === 'Tab') {
            logViolation('keyboard_shortcut', 'Alt+Tab detected');
        }
    }

    // Question navigation
    function goToQuestion(index) {
        if (index < 0 || index >= config.totalQuestions) return;
        
        // Hide all questions
        document.querySelectorAll('.question-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        
        // Show selected question
        const targetQuestion = document.getElementById(`question-${index}`);
        targetQuestion.classList.remove('hidden');
        targetQuestion.classList.add('fade-in');
        
        // Update navigation highlight
        highlightCurrentQuestion(index);
        
        currentQuestion = index;
    }

    // Save answer
    async function selectOption(questionId, optionId, questionIndex) {
        // Update UI immediately for better UX
        const allOptions = document.querySelectorAll(`[id^="option-${questionId}-"]`);
        allOptions.forEach(opt => {
            opt.classList.remove('selected', 'border-indigo-500', 'bg-indigo-50');
            const checkbox = opt.querySelector('div:first-child');
            checkbox.classList.remove('bg-indigo-600', 'border-indigo-600');
            checkbox.classList.add('bg-white', 'border-slate-300');
            checkbox.innerHTML = `<span class="text-slate-500 font-semibold text-sm">${checkbox.dataset.label || ''}</span>`;
        });
        
        const selectedOption = document.getElementById(`option-${questionId}-${optionId}`);
        selectedOption.classList.add('selected', 'border-indigo-500', 'bg-indigo-50');
        const selectedCheckbox = selectedOption.querySelector('div:first-child');
        selectedCheckbox.classList.remove('bg-white', 'border-slate-300');
        selectedCheckbox.classList.add('bg-indigo-600', 'border-indigo-600');
        selectedCheckbox.innerHTML = '<i class="bi bi-check text-white text-lg"></i>';
        
        try {
            const response = await fetch(config.endpoints.saveAnswer, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({
                    question_id: questionId,
                    option_id: optionId
                })
            });
            
            if (response.ok) {
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
            }
        } catch (err) {
            console.error('Error saving answer:', err);
        }
    }

    async function saveEssayAnswer(questionId, questionIndex) {
        const textarea = document.getElementById(`essay-${questionId}`);
        const answer = textarea.value.trim();
        
        if (!answer) return;
        
        try {
            const response = await fetch(config.endpoints.saveAnswer, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                },
                body: JSON.stringify({
                    question_id: questionId,
                    essay_answer: answer
                })
            });
            
            if (response.ok) {
                answeredQuestions.add(questionId);
                updateNavButton(questionIndex, true);
                updateProgressBar();
            }
        } catch (err) {
            console.error('Error saving essay:', err);
        }
    }

    function updateNavButton(index, answered) {
        const btn = document.getElementById(`nav-btn-${index}`);
        if (answered) {
            btn.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            btn.classList.add('bg-gradient-to-br', 'from-emerald-500', 'to-emerald-600', 'text-white', 'shadow-lg', 'shadow-emerald-500/30');
        }
    }

    // Submit
    function confirmSubmit() {
        const answered = answeredQuestions.size;
        const total = config.totalQuestions;
        const percentage = Math.round((answered / total) * 100);
        
        document.getElementById('submit-summary').innerHTML = `
            <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-600">Soal Terjawab</span>
                    <span class="text-2xl font-bold text-slate-800">${answered}/${total}</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                </div>
            </div>
            ${answered < total ? `
            <div class="flex items-start space-x-3 bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-lg mt-0.5"></i>
                <p class="text-amber-800 text-sm">Masih ada <strong>${total - answered} soal</strong> yang belum dijawab!</p>
            </div>
            ` : `
            <div class="flex items-start space-x-3 bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-4">
                <i class="bi bi-check-circle-fill text-emerald-600 text-lg mt-0.5"></i>
                <p class="text-emerald-800 text-sm">Semua soal telah dijawab. Bagus!</p>
            </div>
            `}
            <p class="text-slate-600 text-center">Apakah Anda yakin ingin mengumpulkan ujian?</p>
        `;
        
        document.getElementById('submit-modal').classList.remove('hidden');
    }

    function closeSubmitModal() {
        document.getElementById('submit-modal').classList.add('hidden');
    }

    async function autoSubmit() {
        try {
            const response = await fetch(config.endpoints.autoSubmit, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                }
            });
            
            const data = await response.json();
            
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (err) {
            console.error('Auto submit error:', err);
            // Fallback to form submit
            document.getElementById('submit-form').submit();
        }
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function(e) {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        clearInterval(snapshotInterval);
        clearInterval(timerInterval);
        clearInterval(heartbeatInterval);
        
        // Warn about leaving
        e.preventDefault();
        e.returnValue = '';
    });
</script>
@endpush
