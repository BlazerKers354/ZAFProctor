<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\MonitorController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ProctoringController;
use App\Http\Controllers\SnapshotController;
use App\Http\Controllers\GuideController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

// Download Panduan Pengguna PDF
Route::get('/panduan-pengguna/download', [GuideController::class, 'download'])->name('guide.download');

// Guest Routes (Auth)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
    
    // Registration routes - semua registrasi dilakukan di halaman login
    Route::get('register', function() {
        return redirect()->route('login');
    })->name('register');
    Route::get('register/siswa', function() {
        return redirect()->route('login');
    })->name('register.student.form');
    Route::post('register/siswa', [RegisterController::class, 'storeStudent'])->name('register.student');
    Route::get('register/guru', function() {
        return redirect()->route('login');
    })->name('register.teacher.form');
    Route::post('register/guru', [RegisterController::class, 'storeTeacher'])->name('register.teacher');
    
    // Forgot password - redirect ke login karena sudah terintegrasi
    Route::get('forgot-password', function() {
        return redirect()->route('login');
    })->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'update'])->name('password.update.reset');
});

// Email Verification Routes (harus diluar auth middleware agar URL bisa digenerate)
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// Authenticated Routes
Route::middleware(['auth', 'check.active'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    
    // Email Verification Routes
    Route::get('email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::post('email/verification-notification', [VerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Proctoring Snapshots (authenticated access)
    Route::get('proctoring/snapshot/{log}', [SnapshotController::class, 'show'])->name('proctoring.snapshot.view');
    
    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Users Management
        Route::get('users/pending', [UserController::class, 'pendingApproval'])->name('users.pending');
        Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Classes Management
        Route::resource('classes', ClassController::class);
        Route::post('classes/{class}/students', [ClassController::class, 'addStudents'])->name('classes.add-students');
        Route::delete('classes/{class}/students/{student}', [ClassController::class, 'removeStudent'])->name('classes.remove-student');
        
        // Courses Management
        Route::resource('courses', CourseController::class);
        Route::post('courses/{course}/students', [CourseController::class, 'addStudents'])->name('courses.add-students');
        Route::delete('courses/{course}/students/{student}', [CourseController::class, 'removeStudent'])->name('courses.remove-student');
    });
    
    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        // Exam Management
        Route::resource('exams', TeacherExamController::class);
        Route::post('exams/{exam}/publish', [TeacherExamController::class, 'publish'])->name('exams.publish');
        Route::post('exams/{exam}/duplicate', [TeacherExamController::class, 'duplicate'])->name('exams.duplicate');
        Route::post('exams/{exam}/regenerate-token', [TeacherExamController::class, 'regenerateToken'])->name('exams.regenerate-token');
        
        // Question Management
        Route::prefix('exams/{exam}')->name('questions.')->group(function () {
            Route::get('questions', [QuestionController::class, 'index'])->name('index');
            Route::get('questions/create', [QuestionController::class, 'create'])->name('create');
            Route::post('questions', [QuestionController::class, 'store'])->name('store');
            Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
            Route::put('questions/{question}', [QuestionController::class, 'update'])->name('update');
            Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('destroy');
            Route::post('questions/reorder', [QuestionController::class, 'reorder'])->name('reorder');
            
            // New question features
            Route::get('questions/{question}/detail', [QuestionController::class, 'detail'])->name('detail');
            Route::post('questions/duplicate', [QuestionController::class, 'duplicate'])->name('duplicate');
            Route::post('questions/delete-multiple', [QuestionController::class, 'deleteMultiple'])->name('delete-multiple');
            Route::get('questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('download-template');
            Route::get('questions/export', [QuestionController::class, 'export'])->name('export');
            Route::post('questions/import', [QuestionController::class, 'import'])->name('import');
        });
        
        // Monitoring
        Route::prefix('exams/{exam}/monitor')->name('monitor.')->group(function () {
            Route::get('/', [MonitorController::class, 'index'])->name('index');
            Route::get('live', [MonitorController::class, 'liveData'])->name('live');
            Route::get('attempts/{attempt}', [MonitorController::class, 'attempt'])->name('attempt');
            Route::get('attempts/{attempt}/logs', [MonitorController::class, 'logs'])->name('logs');
            Route::post('attempts/{attempt}/terminate', [MonitorController::class, 'terminate'])->name('terminate');
            Route::post('attempts/{attempt}/logs/review', [MonitorController::class, 'reviewLogs'])->name('review-logs');
        });
        
        // Grading
        Route::get('attempts/{attempt}/grade', [TeacherExamController::class, 'gradeForm'])->name('exams.grade');
        Route::post('attempts/{attempt}/grade', [TeacherExamController::class, 'submitGrade'])->name('exams.submit-grade');
        Route::get('exams/{exam}/results', [TeacherExamController::class, 'results'])->name('exams.results');
        Route::get('exams/{exam}/export', [TeacherExamController::class, 'export'])->name('exams.export');
        Route::patch('exams/{exam}/settings', [TeacherExamController::class, 'updateSettings'])->name('exams.update-settings');
    });
    
    // Student Routes
    Route::prefix('student')->name('student.')->middleware('role:student')->group(function () {
        // Exams
        Route::get('exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::get('exams/{exam}/pre-check', [StudentExamController::class, 'preCheck'])->name('exams.pre-check');
        Route::post('exams/{exam}/start', [StudentExamController::class, 'start'])
            ->middleware('throttle:5,1')
            ->name('exams.start');
        
        // Active Exam Session
        Route::middleware('exam.in-progress')->group(function () {
            Route::get('attempts/{attempt}/take', [StudentExamController::class, 'take'])->name('exams.take');
            Route::post('attempts/{attempt}/save-answer', [StudentExamController::class, 'saveAnswer'])
                ->middleware('throttle:60,1') // Max 60 requests per minute
                ->name('exams.save-answer');
            Route::post('attempts/{attempt}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
            Route::post('attempts/{attempt}/auto-submit', [StudentExamController::class, 'autoSubmit'])->name('exams.auto-submit');
            Route::post('attempts/{attempt}/sync-time', [StudentExamController::class, 'syncTime'])->name('exams.sync-time');
            
            // Proctoring
            Route::get('attempts/{attempt}/proctoring/settings', [ProctoringController::class, 'settings'])
                ->name('proctoring.settings');
            Route::post('attempts/{attempt}/proctoring/violation', [ProctoringController::class, 'logViolation'])
                ->middleware('throttle:30,1') // Max 30 violations logged per minute
                ->name('proctoring.violation');
            Route::post('attempts/{attempt}/proctoring/snapshot', [ProctoringController::class, 'uploadSnapshot'])
                ->middleware('throttle:10,1') // Max 10 snapshots per minute
                ->name('proctoring.snapshot');
            Route::post('attempts/{attempt}/proctoring/heartbeat', [ProctoringController::class, 'heartbeat'])
                ->middleware('throttle:5,1') // Max 5 heartbeats per minute
                ->name('proctoring.heartbeat');
        });
        
        // Results
        Route::get('attempts/{attempt}/result', [StudentExamController::class, 'result'])->name('exams.result');
    });
});
