<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="rounded-lg shadow bg-white dark:bg-gray-900 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">🔍 Filter Ranking</h3>
            <div class="grid grid-cols-1 gap-4">
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

        <!-- Leaderboard Table -->
        <div class="rounded-lg shadow bg-white dark:bg-gray-900">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">🏆 Ranking Siswa</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Berdasarkan total poin yang dikumpulkan
                    @if($selectedClass)
                        • Kelas: {{ $this->getClasses()->firstWhere('id', $selectedClass)->nama_kelas ?? 'Unknown' }}
                    @endif
                    @if($selectedSubject)
                        • Mata Pelajaran:
                        {{ $this->getSubjects()->firstWhere('id', $selectedSubject)->nama_mapel ?? 'Unknown' }}
                    @endif
                </p>
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
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kelas
                            </th>
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
                        @forelse($this->getLeaderboard() as $student)
                            <tr>
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
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student->nama }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $student->nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $student->nama_kelas ?? 'Belum Diset' }}
                                    </span>
                                </td>
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
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data siswa untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>