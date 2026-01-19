@extends('layouts.exam')

@section('content')
    <div id="exam-app" class="min-h-screen flex flex-col">
        <!-- Top Bar -->
        <div class="bg-gray-800 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-50">
            <div class="flex items-center space-x-4">
                <h1 class="text-lg font-semibold">{{ $attempt->exam->title }}</h1>
                <span class="text-gray-400">|</span>
                <span class="text-sm text-gray-300">{{ $attempt->exam->course->name }}</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <!-- Camera Preview -->
                <div class="relative">
                    <video id="camera-preview" class="w-24 h-18 rounded bg-gray-700 object-cover" autoplay muted playsinline></video>
                    <div id="camera-status" class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-green-500 border-2 border-gray-800"></div>
                </div>
                
                <!-- Timer -->
                <div id="timer" class="flex items-center space-x-2 bg-gray-700 px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="timer-display" class="font-mono text-xl font-bold">--:--</span>
                </div>
                
                <!-- Violation Counter -->
                <div id="violation-counter" class="flex items-center space-x-2 bg-red-600 px-3 py-2 rounded-lg" style="display: none;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span id="violation-count" class="font-bold">0</span>
                </div>
                
                <!-- Submit Button -->
                <button onclick="confirmSubmit()" 
                        class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-medium transition">
                    Kumpulkan Ujian
                </button>
            </div>
        </div>
        
        <!-- Warning Banner -->
        <div id="warning-banner" class="bg-red-600 text-white text-center py-2 hidden">
            <span id="warning-message">Peringatan!</span>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex">
            <!-- Question Navigation Sidebar -->
            <div class="w-64 bg-gray-800 p-4 overflow-y-auto">
                <h3 class="text-white font-medium mb-4">Navigasi Soal</h3>
                <div class="grid grid-cols-5 gap-2" id="question-nav">
                    @foreach($questions as $index => $question)
                        <button onclick="goToQuestion({{ $index }})"
                                id="nav-btn-{{ $index }}"
                                class="w-10 h-10 rounded-lg text-sm font-medium transition
                                       {{ isset($answeredQuestions[$question->id]) ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                
                <div class="mt-6 text-sm text-gray-400">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-4 h-4 bg-green-600 rounded"></div>
                        <span>Sudah dijawab</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-gray-600 rounded"></div>
                        <span>Belum dijawab</span>
                    </div>
                </div>
            </div>
            
            <!-- Question Content -->
            <div class="flex-1 bg-gray-100 p-6 overflow-y-auto">
                <div id="questions-container">
                    @foreach($questions as $index => $question)
                        <div id="question-{{ $index }}" 
                             class="question-panel bg-white rounded-lg shadow-lg p-6 mb-4 {{ $index === 0 ? '' : 'hidden' }}">
                            
                            <!-- Question Header -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500">Soal {{ $index + 1 }} dari {{ $questions->count() }}</span>
                                <span class="text-sm font-medium text-indigo-600">{{ $question->points }} poin</span>
                            </div>
                            
                            <!-- Question Text -->
                            <div class="prose max-w-none mb-6">
                                <p class="text-lg text-gray-800">{!! nl2br(e($question->question)) !!}</p>
                                
                                @if($question->question_image)
                                    <img src="{{ asset('storage/' . $question->question_image) }}" 
                                         alt="Question Image" 
                                         class="mt-4 max-w-lg rounded-lg">
                                @endif
                            </div>
                            
                            <!-- Answer Options -->
                            @if($question->isMultipleChoice())
                                <div class="space-y-3">
                                    @foreach($question->options as $option)
                                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition
                                                      {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}"
                                               onclick="selectOption({{ $question->id }}, {{ $option->id }}, {{ $index }})">
                                            <input type="radio" 
                                                   name="question_{{ $question->id }}" 
                                                   value="{{ $option->id }}"
                                                   class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                                   {{ isset($answeredQuestions[$question->id]) && $answeredQuestions[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <span class="font-medium text-gray-700">{{ $option->option_label }}.</span>
                                                <span class="text-gray-800 ml-2">{{ $option->option_text }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <!-- Essay Answer -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Anda:</label>
                                    <textarea id="essay-{{ $question->id }}"
                                              rows="10"
                                              class="w-full border border-gray-300 rounded-lg p-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                              placeholder="Tulis jawaban Anda di sini..."
                                              onblur="saveEssayAnswer({{ $question->id }}, {{ $index }})"
                                    >{{ isset($answeredQuestions[$question->id]) ? $answeredQuestions[$question->id]->essay_answer : '' }}</textarea>
                                </div>
                            @endif
                            
                            <!-- Navigation Buttons -->
                            <div class="flex justify-between mt-6 pt-6 border-t">
                                <button onclick="goToQuestion({{ $index - 1 }})" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition
                                               {{ $index === 0 ? 'invisible' : '' }}">
                                    &larr; Sebelumnya
                                </button>
                                
                                @if($index === $questions->count() - 1)
                                    <button onclick="confirmSubmit()" 
                                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Kumpulkan Ujian
                                    </button>
                                @else
                                    <button onclick="goToQuestion({{ $index + 1 }})" 
                                            class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                                        Selanjutnya &rarr;
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Konfirmasi Pengumpulan</h3>
            <div id="submit-summary" class="mb-4">
                <!-- Will be filled by JS -->
            </div>
            <div class="flex space-x-4">
                <button onclick="closeSubmitModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Kembali
                </button>
                <form id="submit-form" action="{{ route('student.exams.submit', $attempt) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Ya, Kumpulkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Fullscreen Warning Modal -->
    <div id="fullscreen-modal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Mode Fullscreen Diperlukan</h3>
            <p class="text-gray-600 mb-6">
                Ujian ini memerlukan mode fullscreen. Silakan klik tombol di bawah untuk melanjutkan.
            </p>
            <button onclick="enterFullscreen()" 
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Aktifkan Fullscreen
            </button>
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
                document.getElementById('timer').classList.add('bg-red-600');
                document.getElementById('timer').classList.remove('bg-gray-700');
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
        document.getElementById(`question-${index}`).classList.remove('hidden');
        
        // Update navigation
        document.querySelectorAll('#question-nav button').forEach((btn, i) => {
            if (i === index) {
                btn.classList.add('ring-2', 'ring-white');
            } else {
                btn.classList.remove('ring-2', 'ring-white');
            }
        });
        
        currentQuestion = index;
    }

    // Save answer
    async function selectOption(questionId, optionId, questionIndex) {
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
            }
        } catch (err) {
            console.error('Error saving essay:', err);
        }
    }

    function updateNavButton(index, answered) {
        const btn = document.getElementById(`nav-btn-${index}`);
        if (answered) {
            btn.classList.remove('bg-gray-600', 'text-gray-300');
            btn.classList.add('bg-green-600', 'text-white');
        }
    }

    // Submit
    function confirmSubmit() {
        const answered = answeredQuestions.size;
        const total = config.totalQuestions;
        
        document.getElementById('submit-summary').innerHTML = `
            <p class="text-gray-600">Anda telah menjawab <strong>${answered}</strong> dari <strong>${total}</strong> soal.</p>
            ${answered < total ? '<p class="text-red-600 mt-2">Masih ada soal yang belum dijawab!</p>' : ''}
            <p class="mt-4 text-gray-800">Apakah Anda yakin ingin mengumpulkan ujian?</p>
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
