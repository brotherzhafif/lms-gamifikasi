<?php

namespace App\Filament\Guru\Resources\JawabanResource\Pages;

use App\Filament\Guru\Resources\JawabanResource;
use App\Models\Progress;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;

class ViewJawaban extends ViewRecord
{
    protected static string $resource = JawabanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Beri Nilai'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('📋 Informasi Tugas')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('modul.judul')
                                        ->label('Judul Tugas')
                                        ->weight(FontWeight::Bold)
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                                    Infolists\Components\TextEntry::make('siswa.nama')
                                        ->label('Nama Siswa')
                                        ->icon('heroicon-m-user'),

                                    Infolists\Components\TextEntry::make('siswa.nis')
                                        ->label('NIS')
                                        ->icon('heroicon-m-identification'),

                                    Infolists\Components\TextEntry::make('modul.poin_reward')
                                        ->label('Poin Reward')
                                        ->badge()
                                        ->color('warning')
                                        ->formatStateUsing(fn($state) => "⭐ {$state} Poin"),
                                ]),

                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('modul.deadline')
                                        ->label('Deadline')
                                        ->dateTime('d/m/Y H:i')
                                        ->placeholder('Tidak ada deadline')
                                        ->color(fn($record) => $record->modul->deadline && $record->modul->deadline->isPast() ? 'danger' : 'success')
                                        ->icon('heroicon-m-clock'),

                                    Infolists\Components\TextEntry::make('status')
                                        ->label('Status Jawaban')
                                        ->formatStateUsing(fn($state) => match ($state) {
                                            'belum' => 'Belum Dikerjakan',
                                            'draft' => 'Draft (Belum Dikirim)',
                                            'dikirim' => 'Sudah Dikirim',
                                            'terlambat' => 'Terlambat',
                                            'dinilai' => 'Sudah Dinilai',
                                            default => 'Belum Dikerjakan'
                                        })
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'belum' => 'gray',
                                            'draft' => 'warning',
                                            'dikirim' => 'primary',
                                            'terlambat' => 'danger',
                                            'dinilai' => 'success',
                                            default => 'gray',
                                        }),

                                    Infolists\Components\TextEntry::make('submitted_at')
                                        ->label('Dikirim Pada')
                                        ->dateTime('d/m/Y H:i')
                                        ->placeholder('Belum dikirim'),
                                ]),
                        ])->from('lg'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('📝 Jawaban Siswa')
                    ->schema([
                        Infolists\Components\TextEntry::make('isi_jawaban')
                            ->label('Jawaban')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Belum ada jawaban'),

                        Infolists\Components\RepeatableEntry::make('url_file')
                            ->label('File Pendukung')
                            ->schema([
                                Infolists\Components\TextEntry::make('.')
                                    ->formatStateUsing(function ($state) {
                                        $filename = basename($state);
                                        return "📎 {$filename}";
                                    })
                                    ->url(fn($state) => asset('storage/' . $state))
                                    ->openUrlInNewTab(),
                            ])
                            ->visible(fn($record) => !empty($record->url_file))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('📊 Penilaian')
                    ->schema([
                        Infolists\Components\TextEntry::make('nilai')
                            ->label('Nilai')
                            ->formatStateUsing(fn($state) => $state ? "{$state}/100" : 'Belum Dinilai')
                            ->badge()
                            ->color(function ($state) {
                                if (!$state)
                                    return 'gray';
                                return $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger');
                            }),

                        Infolists\Components\TextEntry::make('komentar_guru')
                            ->label('Komentar/Feedback')
                            ->prose()
                            ->markdown()
                            ->placeholder('Belum ada komentar')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
