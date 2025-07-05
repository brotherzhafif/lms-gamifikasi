<?php

namespace App\Filament\Siswa\Resources\ModulResource\Pages;

use App\Filament\Siswa\Resources\ModulResource;
use App\Models\Progress;
use App\Models\Jawaban;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;

class ViewModul extends ViewRecord
{
    protected static string $resource = ModulResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('📋 Informasi Modul')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('mataPelajaran.nama_mapel')
                                        ->label('Mata Pelajaran')
                                        ->badge()
                                        ->color('primary'),

                                    Infolists\Components\TextEntry::make('judul')
                                        ->label('Judul Modul')
                                        ->weight(FontWeight::Bold)
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                    Infolists\Components\TextEntry::make('guru.nama')
                                        ->label('Pengajar')
                                        ->icon('heroicon-m-user'),

                                    Infolists\Components\TextEntry::make('jenis')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'materi' => 'success',
                                            'tugas' => 'warning',
                                        }),

                                    Infolists\Components\TextEntry::make('poin_reward')
                                        ->label('Poin Reward')
                                        ->badge()
                                        ->color('warning')
                                        ->formatStateUsing(fn($state) => "⭐ {$state} Poin"),
                                ]),

                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('deadline')
                                        ->label('Deadline')
                                        ->dateTime('d/m/Y H:i')
                                        ->placeholder('Tidak ada deadline')
                                        ->color(fn($record) => $record->deadline && $record->deadline->isPast() ? 'danger' : 'success')
                                        ->icon('heroicon-m-clock'),

                                    Infolists\Components\TextEntry::make('status_completion')
                                        ->label('Status Penyelesaian')
                                        ->getStateUsing(function ($record) {
                                            if ($record->jenis === 'materi') {
                                                $progress = Progress::where('user_id', Auth::id())
                                                    ->where('modul_id', $record->id)
                                                    ->first();
                                                return $progress ? 'Selesai' : 'Belum Selesai';
                                            } else {
                                                $jawaban = Jawaban::where('siswa_id', Auth::id())
                                                    ->where('modul_id', $record->id)
                                                    ->first();

                                                if (!$jawaban)
                                                    return 'Belum dikerjakan';

                                                return match ($jawaban->status) {
                                                    'draft' => 'Draft',
                                                    'dikirim' => 'Sudah dikirim',
                                                    'terlambat' => 'Terlambat',
                                                    'dinilai' => "Dinilai: {$jawaban->nilai}/100",
                                                    default => 'Belum dikerjakan'
                                                };
                                            }
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            if ($record->jenis === 'materi') {
                                                $progress = Progress::where('user_id', Auth::id())
                                                    ->where('modul_id', $record->id)
                                                    ->exists();
                                                return $progress ? 'success' : 'gray';
                                            } else {
                                                $jawaban = Jawaban::where('siswa_id', Auth::id())
                                                    ->where('modul_id', $record->id)
                                                    ->first();

                                                if (!$jawaban)
                                                    return 'danger';

                                                return match ($jawaban->status) {
                                                    'draft' => 'warning',
                                                    'dikirim' => 'info',
                                                    'terlambat' => 'danger',
                                                    'dinilai' => 'success',
                                                    default => 'danger'
                                                };
                                            }
                                        }),
                                ]),
                        ])->from('lg'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('📖 Konten Modul')
                    ->schema([
                        Infolists\Components\TextEntry::make('isi')
                            ->label('')
                            ->prose()
                            ->formatStateUsing(fn($state) => strip_tags($state))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('📎 File Lampiran')
                    ->schema([
                        Infolists\Components\TextEntry::make('file_path')
                            ->label('File Lampiran')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'Tidak ada file lampiran';
                                }

                                $files = is_array($state) ? $state : [$state];
                                $buttons = [];

                                foreach ($files as $file) {
                                    $fileName = basename($file);
                                    $fileUrl = asset('storage/' . $file);
                                    $buttons[] = "<a href='{$fileUrl}' target='_blank' class='inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors duration-200 mr-2 mb-2 no-underline'><svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'></path></svg>{$fileName}</a>";
                                }

                                return implode(' ', $buttons);
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->file_path))
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('💬 Feedback Guru')
                    ->schema([
                        Infolists\Components\TextEntry::make('teacher_comment')
                            ->label('Komentar Guru')
                            ->getStateUsing(function ($record) {
                                $jawaban = Jawaban::where('modul_id', $record->id)
                                    ->where('siswa_id', Auth::id())
                                    ->first();
                                return $jawaban?->komentar_guru;
                            })
                            ->prose()
                            ->placeholder('Belum ada komentar dari guru')
                            ->columnSpanFull(),
                    ])
                    ->visible(function ($record) {
                        if ($record->jenis !== 'tugas')
                            return false;

                        $jawaban = Jawaban::where('modul_id', $record->id)
                            ->where('siswa_id', Auth::id())
                            ->first();
                        return $jawaban && $jawaban->komentar_guru;
                    })
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action untuk tandai selesai (materi) - moved here from table
            Actions\Action::make('tandai_selesai')
                ->label('✅ Tandai Selesai')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(function () {
                    $record = $this->getRecord();
                    return $record->jenis === 'materi' &&
                        !Progress::where('user_id', Auth::id())
                            ->where('modul_id', $record->id)
                            ->exists();
                })
                ->action(function () {
                    $record = $this->getRecord();
                    Progress::create([
                        'user_id' => Auth::id(),
                        'modul_id' => $record->id,
                        'jumlah_poin' => $record->poin_reward,
                        'jenis_aktivitas' => 'selesai_materi',
                        'keterangan' => "Menyelesaikan materi: {$record->judul}",
                    ]);

                    Notification::make()
                        ->title('Materi berhasil diselesaikan!')
                        ->body("Anda mendapat {$record->poin_reward} poin")
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
                })
                ->requiresConfirmation()
                ->modalHeading('Tandai Materi Selesai')
                ->modalSubheading(fn() => "Pastikan Anda sudah membaca dan memahami materi ini. Anda akan mendapat {$this->getRecord()->poin_reward} poin.")
                ->modalIcon('heroicon-o-academic-cap'),

            // Action untuk kerjakan tugas
            Actions\Action::make('kerjakan')
                ->label(function () {
                    $record = $this->getRecord();
                    $jawaban = Jawaban::where('modul_id', $record->id)
                        ->where('siswa_id', Auth::id())
                        ->first();

                    if ($jawaban) {
                        return $jawaban->status === 'draft' ? '✏️ Lanjutkan Mengerjakan' : '👁️ Lihat Jawaban';
                    }

                    return '🚀 Mulai Mengerjakan';
                })
                ->icon(function () {
                    $record = $this->getRecord();
                    $jawaban = Jawaban::where('modul_id', $record->id)
                        ->where('siswa_id', Auth::id())
                        ->first();

                    return $jawaban && $jawaban->status !== 'draft' ? 'heroicon-o-eye' : 'heroicon-o-pencil-square';
                })
                ->color(function () {
                    $record = $this->getRecord();
                    $jawaban = Jawaban::where('modul_id', $record->id)
                        ->where('siswa_id', Auth::id())
                        ->first();

                    return $jawaban && $jawaban->status !== 'draft' ? 'info' : 'primary';
                })
                ->visible(fn() => $this->getRecord()->jenis === 'tugas')
                ->action(function () {
                    $record = $this->getRecord();
                    $jawaban = Jawaban::where('modul_id', $record->id)
                        ->where('siswa_id', Auth::id())
                        ->first();

                    if (!$jawaban) {
                        // Create new answer
                        $jawaban = Jawaban::create([
                            'modul_id' => $record->id,
                            'siswa_id' => Auth::id(),
                            'status' => 'draft',
                        ]);
                        return redirect()->to("/siswa/jawabans/{$jawaban->id}/edit");
                    }

                    // If draft, go to edit; otherwise go to view
                    if ($jawaban->status === 'draft') {
                        return redirect()->to("/siswa/jawabans/{$jawaban->id}/edit");
                    } else {
                        return redirect()->to("/siswa/jawabans/{$jawaban->id}");
                    }
                }),

            // Back to list action
            Actions\Action::make('back')
                ->label('⬅️ Kembali ke Daftar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => $this->getResource()::getUrl('index')),
        ];
    }
}
