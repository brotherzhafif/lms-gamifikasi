<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JawabanResource\Pages;
use App\Models\Jawaban;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JawabanResource extends Resource
{
    protected static ?string $model = Jawaban::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Modul & Pembelajaran';

    protected static ?string $navigationLabel = 'Semua Jawaban';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('modul_id')
                    ->relationship('modul', 'judul')
                    ->required()
                    ->disabled(fn($context) => $context === 'edit'),

                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->required()
                    ->disabled(fn($context) => $context === 'edit'),

                Forms\Components\Textarea::make('isi_jawaban')
                    ->label('Jawaban Siswa')
                    ->columnSpanFull()
                    ->rows(5),

                Forms\Components\FileUpload::make('url_file')
                    ->label('File Jawaban')
                    ->multiple()
                    ->directory('jawaban-files')
                    ->downloadable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nilai')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label('Nilai'),

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

                Forms\Components\DateTimePicker::make('submitted_at')
                    ->label('Tanggal Submit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modul.judul')
                    ->label('Modul')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),

                Tables\Columns\TextColumn::make('modul.mataPelajaran.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('modul.guru.nama')
                    ->label('Guru')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'belum',
                        'warning' => 'draft',
                        'primary' => 'dikirim',
                        'secondary' => 'terlambat',
                        'success' => 'dinilai',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'belum' => 'Belum Dikerjakan',
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                        default => 'Belum Dikerjakan'
                    }),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state ? ($state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')) : 'gray')
                    ->formatStateUsing(fn($state) => $state ? $state . '/100' : 'Belum Dinilai'),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tanggal Submit')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Belum dikirim'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->relationship('modul', 'judul')
                    ->label('Modul'),

                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->relationship('modul.mataPelajaran', 'nama_mapel')
                    ->label('Mata Pelajaran'),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->relationship('siswa.kelas', 'nama_kelas')
                    ->label('Kelas'),

                Tables\Filters\SelectFilter::make('guru_id')
                    ->relationship('modul.guru', 'nama')
                    ->label('Guru'),

                Tables\Filters\SelectFilter::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->label('Siswa'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
