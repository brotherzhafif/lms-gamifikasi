<div
    class="filament-widget bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg overflow-hidden">

    <!-- Header with Profile Info -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-700 dark:to-indigo-800 p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <!-- Profile Section -->
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-6">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-1">
                            {{ $this->getViewData()['user']->nama }}
                        </h3>
                        <p class="text-blue-100 mb-2">
                            {{ $this->getViewData()['user']->kelas?->nama_kelas ?? 'Belum ada kelas' }}
                        </p>
                        <div class="flex items-center space-x-4 text-blue-100 text-sm">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span>{{ $this->getViewData()['user']->nis }}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ $this->getViewData()['user']->email }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Logout Section -->
            <div class="flex flex-col space-y-4 lg:items-end">
                <!-- Status Badge -->
                <div
                    class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                    Online
                </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ filament()->getLogoutUrl() }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur border border-white/30 rounded-lg text-white text-sm font-medium hover:bg-white/30 transition-all duration-200 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="h-4"></div>

    <!-- Content Section -->
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Points Card -->
            <div
                class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-yellow-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Poin</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->getViewData()['totalPoin']) }}
                    </p>
                </div>
            </div>

            <!-- Class Ranking Card -->
            <div
                class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-emerald-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Ranking Kelas</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if($this->getViewData()['classRanking'])
                            #{{ $this->getViewData()['classRanking']->ranking }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            <!-- Class Info Card -->
            <div
                class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-purple-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Kelas</h4>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        @if($this->getViewData()['user']->kelas)
                            {{ $this->getViewData()['user']->kelas->nama_kelas }}
                        @else
                            Belum ada kelas
                        @endif
                    </p>
                </div>
            </div>

            <!-- Student ID Card -->
            <div
                class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border border-cyan-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">NIS</h4>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $this->getViewData()['user']->nis }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>