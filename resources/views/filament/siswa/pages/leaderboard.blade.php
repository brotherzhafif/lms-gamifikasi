<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Current User Stats -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
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

        <!-- Leaderboard Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">🏆 Ranking Siswa</h3>
                <p class="text-sm text-gray-600 mt-1">Berdasarkan total poin yang dikumpulkan</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ranking
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Siswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NIS
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Poin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Modul Selesai
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->getLeaderboard() as $student)
                            <tr class="{{ $student->id === auth()->id() ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($student->ranking <= 3)
                                            <span class="text-2xl">
                                                {{ $student->ranking === 1 ? '🥇' : ($student->ranking === 2 ? '🥈' : '🥉') }}
                                            </span>
                                        @else
                                            <span class="text-sm font-medium text-gray-900">
                                                #{{ $student->ranking }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->nama }}
                                            @if($student->id === auth()->id())
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Saya
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $student->nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⭐ {{ $student->total_poin }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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