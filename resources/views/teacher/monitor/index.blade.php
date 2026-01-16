@extends('layouts.app')

@section('title', 'Monitor Ujian')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Monitor Ujian: {{ $exam->title }}</h1>
            <p class="text-sm text-gray-500">{{ $exam->course->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium animate-pulse">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                Live
            </span>
            <a href="{{ route('teacher.exams.show', $exam) }}" class="text-indigo-600 hover:text-indigo-900">
                Kembali ke Detail
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-3xl font-bold text-gray-900" id="total-enrolled">{{ $exam->course->students_count }}</div>
            <div class="text-sm text-gray-500">Total Peserta</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-3xl font-bold text-green-600" id="in-progress">{{ $activeAttempts->count() }}</div>
            <div class="text-sm text-gray-500">Sedang Mengerjakan</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-3xl font-bold text-blue-600" id="submitted">{{ $submittedCount }}</div>
            <div class="text-sm text-gray-500">Sudah Selesai</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-3xl font-bold text-red-600" id="violations">{{ $totalViolations }}</div>
            <div class="text-sm text-gray-500">Total Pelanggaran</div>
        </div>
    </div>
    
    <!-- Token Display -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-yellow-800">Token Akses Ujian:</p>
                <p class="text-2xl font-mono font-bold text-yellow-900">{{ $exam->access_token }}</p>
            </div>
            <button onclick="copyToken('{{ $exam->access_token }}')" 
                    class="px-4 py-2 bg-yellow-200 text-yellow-800 rounded-lg hover:bg-yellow-300 transition">
                Salin Token
            </button>
        </div>
    </div>
    
    <!-- Active Attempts Grid -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Peserta Aktif</h2>
            <button onclick="refreshData()" class="text-sm text-indigo-600 hover:text-indigo-900">
                🔄 Refresh
            </button>
        </div>
        
        @if($activeAttempts->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p>Belum ada peserta yang sedang mengerjakan ujian.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-6" id="attempts-grid">
                @foreach($activeAttempts as $attempt)
                    <div class="border rounded-lg overflow-hidden {{ $attempt->violation_count > 0 ? 'border-red-300' : 'border-gray-200' }} hover:shadow-lg transition"
                         id="attempt-{{ $attempt->id }}">
                        <!-- Camera Snapshot -->
                        <div class="aspect-video bg-gray-800 relative">
                            @if($attempt->latestSnapshot)
                                <img src="{{ asset('storage/' . $attempt->latestSnapshot->snapshot_path) }}" 
                                     alt="Camera" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Violation Badge -->
                            @if($attempt->violation_count > 0)
                                <div class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $attempt->violation_count }} ⚠️
                                </div>
                            @endif
                            
                            <!-- Camera Status -->
                            <div class="absolute bottom-2 right-2">
                                <span class="w-3 h-3 rounded-full {{ $attempt->camera_enabled ? 'bg-green-500' : 'bg-red-500' }} inline-block"></span>
                            </div>
                        </div>
                        
                        <!-- Student Info -->
                        <div class="p-3">
                            <div class="font-medium text-gray-900 text-sm truncate">{{ $attempt->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $attempt->user->email }}</div>
                            
                            <div class="flex items-center justify-between mt-2 text-xs">
                                <span class="text-gray-500">
                                    {{ $attempt->answers_count ?? 0 }}/{{ $exam->questions_count }} soal
                                </span>
                                <span class="text-gray-500">
                                    {{ $attempt->formatted_remaining_time }}
                                </span>
                            </div>
                            
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('teacher.monitor.attempt', $attempt) }}"
                                   class="flex-1 text-center text-xs bg-gray-100 text-gray-700 py-1 rounded hover:bg-gray-200">
                                    Detail
                                </a>
                                @if($attempt->violation_count > 0)
                                    <a href="{{ route('teacher.monitor.logs', $attempt) }}"
                                       class="flex-1 text-center text-xs bg-red-100 text-red-700 py-1 rounded hover:bg-red-200">
                                        Log
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Recent Violations -->
    @if($recentViolations->isNotEmpty())
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h2 class="text-lg font-medium text-red-800">⚠️ Pelanggaran Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentViolations as $violation)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($violation->snapshot_path)
                                    <img src="{{ asset('storage/' . $violation->snapshot_path) }}" 
                                         alt="Snapshot" class="w-12 h-9 object-cover rounded">
                                @else
                                    <div class="w-12 h-9 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $violation->attempt->user->name }}</div>
                                <div class="text-sm text-gray-500">
                                    <span class="inline-flex px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">
                                        {{ $violation->violation_type }}
                                    </span>
                                    {{ $violation->description }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $violation->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function copyToken(token) {
        navigator.clipboard.writeText(token).then(() => {
            alert('Token disalin: ' + token);
        });
    }
    
    function refreshData() {
        window.location.reload();
    }
    
    // Auto refresh every 30 seconds
    setInterval(refreshData, 30000);
</script>
@endpush
