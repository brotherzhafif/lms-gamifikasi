<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\JawabanResource\Pages;
use App\Models\Jawaban;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class JawabanResource extends Resource
{
    protected static ?string $model = Jawaban::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?string $navigationLabel = 'Jawaban Siswa';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('modul', function ($query) {
                $query->where('guru_id', Auth::id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('modul_id')
                    ->relationship('modul', 'judul', fn(Builder $query) => $query->where('guru_id', Auth::id()))
                    ->disabled(),
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->disabled(),
                Forms\Components\Textarea::make('isi_jawaban')
                    ->label('Jawaban Siswa')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('url_file')
                    ->label('File Jawaban')
                    ->multiple()
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nilai')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Select::make('status')
                    ->options([
                        'belum' => 'Belum Dikerjakan',
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('komentar_guru')
                    ->label('Komentar/Feedback')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modul.judul')
                    ->label('Modul')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'belum',
                        'warning' => 'draft',
                        'primary' => 'dikirim',
                        'danger' => 'terlambat',
                        'success' => 'dinilai',
                    ]),
                Tables\Columns\TextColumn::make('nilai')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tanggal Submit')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum' => 'Belum Dikerjakan',
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                    ]),
                Tables\Filters\SelectFilter::make('modul_id')
                    ->relationship('modul', 'judul', fn(Builder $query) => $query->where('guru_id', Auth::id()))
                    ->label('Modul'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Nilai')
                    ->modalHeading('Beri Nilai & Feedback'),
                Tables\Actions\Action::make('auto_progress')
                    ->label('Tambah ke Progress')
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn($record) => $record->status === 'dinilai' && $record->nilai !== null)
                    ->action(function (Jawaban $record) {
                        Progress::firstOrCreate([
                            'user_id' => $record->siswa_id,
                            'modul_id' => $record->modul_id,
                        ], [
                            'jumlah_poin' => $record->modul->poin_reward,
                            'jenis_aktivitas' => 'selesai_' . $record->modul->jenis,
                            'keterangan' => "Menyelesaikan {$record->modul->jenis}: {$record->modul->judul}",
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJawabans::route('/'),
            'view' => Pages\ViewJawaban::route('/{record}'),
            'edit' => Pages\EditJawaban::route('/{record}/edit'),
        ];
    }
}
