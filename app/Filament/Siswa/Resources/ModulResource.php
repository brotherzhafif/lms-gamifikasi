<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\ModulResource\Pages;
use App\Models\Modul;
use App\Models\Progress;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ModulResource extends Resource
{
    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Pembelajaran';

    protected static ?string $navigationLabel = 'Daftar Modul';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_active', true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Guru')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('jenis')
                    ->colors([
                        'success' => 'materi',
                        'warning' => 'tugas',
                        'danger' => 'quiz',
                    ]),
                Tables\Columns\TextColumn::make('poin_reward')
                    ->label('Poin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable()
                    ->color(fn($record) => $record->deadline && $record->deadline->isPast() ? 'danger' : null),
                Tables\Columns\IconColumn::make('completed')
                    ->label('Selesai')
                    ->getStateUsing(function ($record) {
                        return Progress::where('user_id', Auth::id())
                            ->where('modul_id', $record->id)
                            ->exists();
                    })
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                        'quiz' => 'Quiz',
                    ]),
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Status Penyelesaian')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('progresses', function ($q) {
                            $q->where('user_id', Auth::id());
                        }),
                        false: fn(Builder $query) => $query->whereDoesntHave('progresses', function ($q) {
                            $q->where('user_id', Auth::id());
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('kerjakan')
                    ->label('Kerjakan')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn($record) => in_array($record->jenis, ['tugas', 'quiz']))
                    ->url(fn($record) => route('siswa.jawaban.create', ['modul' => $record->id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuls::route('/'),
            'view' => Pages\ViewModul::route('/{record}'),
        ];
    }
}
