<x-filament-panels::page>
    <div class="space-y-6">
        @if(auth()->user()->role === 'murid')
            <!-- Current User Stats -->
            <div class="rounded-lg p-6 bg-primary-600 text-white dark:bg-primary-700">
                <h2 class="text-xl font-bold mb-2">Statistik Saya</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ auth()->user()->progresses->sum('jumlah_poin') }}</div>
                        <div class="text-sm opacity-90">Total Poin</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ auth()->user()->progresses->unique('modul_id')->count() }}</div>
                        <div class="text-sm opacity-90">Modul Selesai</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">
                            #{{ $this->getLeaderboard()->firstWhere('id', auth()->id())->ranking ?? 'N/A' }}
                        </div>
                        <div class="text-sm opacity-90">Peringkat</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters Section (for Admin and Guru only) -->
        @if(auth()->user()->role !== 'murid')
            <div class="rounded-lg shadow bg-white dark:bg-gray-900 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">🔍 Filter Ranking</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(auth()->user()->role === 'admin')
                        <!-- Subject Filter for Admin -->
                        <div>
                            <label for="subject-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Mata Pelajaran
                            </label>
                            <select id="subject-filter" wire:model.live="selectedSubject"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($this->getSubjects() as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Class Filter -->
                    <div>
                        <label for="class-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kelas
                        </label>
                        <select id="class-filter" wire:model.live="selectedClass"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Semua Kelas</option>
                            @foreach($this->getClasses() as $class)
                                <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <!-- Leaderboard Table -->
        <div class="rounded-lg shadow bg-white dark:bg-gray-900">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">🏆 Ranking Siswa</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Berdasarkan total poin yang dikumpulkan</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Ranking
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama Siswa
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                NIS
                            </th>
                            @if(auth()->user()->role !== 'murid')
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kelas
                                </th>
                            @endif
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Poin
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Modul Selesai
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getLeaderboard() as $student)
                            <tr class="{{ $student->id === auth()->id() ? 'bg-primary-50 dark:bg-primary-900/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($student->ranking <= 3)
                                            <span class="text-2xl">
                                                {{ $student->ranking === 1 ? '🥇' : ($student->ranking === 2 ? '🥈' : '🥉') }}
                                            </span>
                                        @else
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                #{{ $student->ranking }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $student->nama }}
                                            @if($student->id === auth()->id())
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                                    Saya
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $student->nis }}
                                </td>
                                @if(auth()->user()->role !== 'murid')
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $student->nama_kelas ?? 'Belum Diset' }}
                                        </span>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        ⭐ {{ $student->total_poin }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $student->modul_selesai }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>