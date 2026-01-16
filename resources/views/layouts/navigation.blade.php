<nav class="bg-indigo-600 shadow-lg" x-data="{ open: false, profileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl">
                        📝 ZAFProctor
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') ? 'border-b-2 border-white' : '' }}">
                                Pengguna
                            </a>
                            <a href="{{ route('admin.users.pending') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('admin.users.pending') ? 'border-b-2 border-white' : '' }}">
                                Persetujuan
                                @php
                                    $pendingCount = \App\Models\User::pendingApproval()->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.classes.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('admin.classes.*') ? 'border-b-2 border-white' : '' }}">
                                Kelas
                            </a>
                            <a href="{{ route('admin.courses.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('admin.courses.*') ? 'border-b-2 border-white' : '' }}">
                                Mata Pelajaran
                            </a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('teacher.exams.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('teacher.exams.*') ? 'border-b-2 border-white' : '' }}">
                                Ujian
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('student.exams.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium text-white hover:text-indigo-200 {{ request()->routeIs('student.exams.*') ? 'border-b-2 border-white' : '' }}">
                                Ujian
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                @auth
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-sm text-white hover:text-indigo-200 focus:outline-none">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                            <span class="ml-2">{{ auth()->user()->name }}</span>
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <div class="px-4 py-2 text-xs text-gray-500">
                                    {{ auth()->user()->role->display_name }}
                                </div>
                                <hr>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profil Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-indigo-200 text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="ml-4 bg-white text-indigo-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-50">Register</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-indigo-200 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Pengguna</a>
                    <a href="{{ route('admin.users.pending') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">
                        Persetujuan
                        @php
                            $pendingCount = \App\Models\User::pendingApproval()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Kelas</a>
                    <a href="{{ route('admin.courses.index') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Mata Pelajaran</a>
                @elseif(auth()->user()->isTeacher())
                    <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Dashboard</a>
                    <a href="{{ route('teacher.exams.index') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Ujian</a>
                @else
                    <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Dashboard</a>
                    <a href="{{ route('student.exams.index') }}" class="block pl-3 pr-4 py-2 text-white hover:bg-indigo-700">Ujian</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
