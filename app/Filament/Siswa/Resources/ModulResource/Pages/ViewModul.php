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
use Illuminate\Support\Facades\Auth;

class ViewModul extends ViewRecord
{
    protected static string $resource = ModulResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Modul')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
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
                                        ->dateTime()
                                        ->placeholder('Tidak ada deadline')
                                        ->color(fn($record) => $record->deadline && $record->deadline->isPast() ? 'danger' : 'success')
                                        ->icon('heroicon-m-clock'),
                                    Infolists\Components\TextEntry::make('status_completion')
                                        ->label('Status Penyelesaian')
                                        ->getStateUsing(function ($record) {
                                            $progress = Progress::where('user_id', Auth::id())
                                                ->where('modul_id', $record->id)
                                                ->first();
                                            return $progress ? 'Selesai' : 'Belum Selesai';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            $progress = Progress::where('user_id', Auth::id())
                                                ->where('modul_id', $record->id)
                                                ->exists();
                                            return $progress ? 'success' : 'gray';
                                        }),
                                ]),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('Konten Modul')
                    ->schema([
                        Infolists\Components\TextEntry::make('isi')
                            ->label('')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('File Lampiran')
                    ->schema([
                        Infolists\Components\TextEntry::make('file_path')
                            ->label('File Terlampir')
                            ->listWithLineBreaks()
                            ->placeholder('Tidak ada file')
                            ->formatStateUsing(function ($state) {
                                if (!$state)
                                    return null;
                                $files = is_array($state) ? $state : [$state];
                                return collect($files)->map(function ($file) {
                                    return "<a href='/storage/{$file}' target='_blank' class='text-blue-600 hover:text-blue-800 underline'>" . basename($file) . "</a>";
                                })->join('<br>');
                            })
                            ->html(),
                    ])
                    ->visible(fn($record) => !empty($record->file_path)),

                Infolists\Components\Section::make('Status Jawaban')
                    ->schema([
                        Infolists\Components\TextEntry::make('answer_status')
                            ->label('Status Jawaban Anda')
                            ->getStateUsing(function ($record) {
                                if ($record->jenis === 'materi') {
                                    return 'Tidak perlu jawaban';
                                }

                                $jawaban = Jawaban::where('modul_id', $record->id)
                                    ->where('siswa_id', Auth::id())
                                    ->first();

                                if (!$jawaban) {
                                    return 'Belum dikerjakan';
                                }

                                return match ($jawaban->status) {
                                    'draft' => 'Draft',
                                    'dikirim' => 'Sudah dikirim',
                                    'dinilai' => "Dinilai: {$jawaban->nilai}/100",
                                    default => 'Belum dikerjakan'
                                };
                            })
                            ->badge()
                            ->color(function ($record) {
                                if ($record->jenis === 'materi')
                                    return 'gray';

                                $jawaban = Jawaban::where('modul_id', $record->id)
                                    ->where('siswa_id', Auth::id())
                                    ->first();

                                if (!$jawaban)
                                    return 'danger';

                                return match ($jawaban->status) {
                                    'draft' => 'warning',
                                    'dikirim' => 'info',
                                    'dinilai' => 'success',
                                    default => 'danger'
                                };
                            }),
                        Infolists\Components\TextEntry::make('teacher_comment')
                            ->label('Komentar Guru')
                            ->getStateUsing(function ($record) {
                                $jawaban = Jawaban::where('modul_id', $record->id)
                                    ->where('siswa_id', Auth::id())
                                    ->first();
                                return $jawaban?->komentar_guru;
                            })
                            ->placeholder('Belum ada komentar')
                            ->visible(function ($record) {
                                $jawaban = Jawaban::where('modul_id', $record->id)
                                    ->where('siswa_id', Auth::id())
                                    ->first();
                                return $jawaban && $jawaban->komentar_guru;
                            }),
                    ])
                    ->visible(fn($record) => $record->jenis === 'tugas'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action untuk tandai selesai (materi)
            Actions\Action::make('tandai_selesai')
                ->label('Tandai Selesai')
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

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
                })
                ->requiresConfirmation()
                ->modalHeading('Tandai Materi Selesai')
                ->modalSubheading(fn() => "Anda akan mendapat {$this->getRecord()->poin_reward} poin"),

            // Action untuk kerjakan tugas
            Actions\Action::make('kerjakan')
                ->label(function () {
                    $record = $this->getRecord();
                    $jawaban = Jawaban::where('modul_id', $record->id)
                        ->where('siswa_id', Auth::id())
                        ->first();

                    if ($jawaban) {
                        return $jawaban->status === 'draft' ? 'Lanjutkan Mengerjakan' : 'Lihat Jawaban';
                    }

                    return 'Mulai Mengerjakan';
                })
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
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
                    }

                    return redirect()->to("/siswa/jawabans/{$jawaban->id}/edit");
                }),

            // Back to list action
            Actions\Action::make('back')
                ->label('Kembali ke Daftar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => $this->getResource()::getUrl('index')),
        ];
    }
}
