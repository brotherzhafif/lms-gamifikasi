<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProgressResource\Pages;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Statistik';

    protected static ?string $navigationLabel = 'Progress & Poin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'nama')
                    ->required(),
                Forms\Components\Select::make('modul_id')
                    ->relationship('modul', 'judul')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_poin')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('jenis_aktivitas')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modul.judul')
                    ->label('Modul')
                    ->searchable()
                    ->limit(30),
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
                        'selesai_quiz' => 'success',
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
                        'selesai_quiz' => 'Selesai Quiz',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'nama')
                    ->label('Siswa'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgress::route('/'),
            'create' => Pages\CreateProgress::route('/create'),
            'view' => Pages\ViewProgress::route('/{record}'),
            'edit' => Pages\EditProgress::route('/{record}/edit'),
        ];
    }
}
