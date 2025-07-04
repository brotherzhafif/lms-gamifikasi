<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\ProgressResource\Pages;
use App\Models\Progress;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Progress Saya';

    protected static ?string $navigationLabel = 'Poin & Progress';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modul.judul')
                    ->label('Modul')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('modul.guru.nama')
                    ->label('Guru')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_poin')
                    ->label('Poin')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('jenis_aktivitas')
                    ->label('Aktivitas')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'selesai_materi' => 'info',
                        'selesai_tugas' => 'warning',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_aktivitas')
                    ->options([
                        'selesai_materi' => 'Selesai Materi',
                        'selesai_tugas' => 'Selesai Tugas',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('total_poin')
                    ->label(fn() => 'Total Poin: ' . Progress::where('user_id', Auth::id())->sum('jumlah_poin'))
                    ->color('success')
                    ->button()
                    ->disabled(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgress::route('/'),
            'view' => Pages\ViewProgress::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
