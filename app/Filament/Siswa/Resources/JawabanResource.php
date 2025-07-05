<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\JawabanResource\Pages;
use App\Models\Jawaban;
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

    protected static ?string $navigationGroup = 'Tugas';

    protected static ?string $navigationLabel = 'Jawaban Saya';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('siswa_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('modul_id')
                    ->relationship('modul', 'judul', fn(Builder $query) => $query->where('is_active', true)->where('jenis', 'tugas'))
                    ->required()
                    ->disabled(fn($context) => $context === 'edit'),

                Forms\Components\Section::make('📋 Informasi Tugas')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('mata_pelajaran')
                                    ->label('Mata Pelajaran')
                                    ->content(fn($record) => $record && $record->modul && $record->modul->mataPelajaran ? $record->modul->mataPelajaran->nama_mapel : '-'),

                                Forms\Components\Placeholder::make('judul_tugas')
                                    ->label('Judul Tugas')
                                    ->content(fn($record) => $record && $record->modul ? $record->modul->judul : '-'),

                                Forms\Components\Placeholder::make('deadline')
                                    ->label('Deadline')
                                    ->content(function ($record) {
                                        if (!$record || !$record->modul || !$record->modul->deadline) {
                                            return 'Tidak ada deadline';
                                        }
                                        return $record->modul->deadline->format('d/m/Y H:i');
                                    }),

                                Forms\Components\Placeholder::make('status_tugas')
                                    ->label('Status')
                                    ->content(function ($record) {
                                        if (!$record)
                                            return '-';

                                        return match ($record->status) {
                                            'belum' => '⏳ Belum Dikerjakan',
                                            'draft' => '📝 Draft (Belum Dikirim)',
                                            'dikirim' => '✅ Sudah Dikirim',
                                            'terlambat' => '⚠️ Terlambat',
                                            'dinilai' => '🎯 Sudah Dinilai',
                                            default => '⏳ Belum Dikerjakan'
                                        };
                                    }),

                                Forms\Components\Placeholder::make('poin_reward')
                                    ->label('Poin Reward')
                                    ->content(fn($record) => $record && $record->modul ? "⭐ {$record->modul->poin_reward} poin" : '-'),

                                Forms\Components\Placeholder::make('guru_pengajar')
                                    ->label('Guru Pengajar')
                                    ->content(fn($record) => $record && $record->modul && $record->modul->guru ? $record->modul->guru->nama : '-')
                                    ->columnSpan(fn($record) => $record && $record->nilai ? 1 : 2),

                                Forms\Components\Placeholder::make('nilai_anda')
                                    ->label('Nilai Anda')
                                    ->content(function ($record) {
                                        if (!$record || !$record->nilai)
                                            return null;

                                        $emoji = $record->nilai >= 80 ? '🏆' : ($record->nilai >= 60 ? '👍' : '💪');
                                        return "{$emoji} {$record->nilai}/100";
                                    })
                                    ->visible(fn($record) => $record && $record->nilai),
                            ])
                            ->visible(fn($context) => $context === 'edit'),
                    ])
                    ->visible(fn($context) => $context === 'edit')
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('📝 Detail Tugas')
                    ->schema([
                        Forms\Components\Placeholder::make('deskripsi_tugas')
                            ->label('Deskripsi Tugas')
                            ->content(function ($record) {
                                if (!$record || !$record->modul || !$record->modul->isi) {
                                    return 'Tidak ada deskripsi tugas';
                                }
                                // Remove HTML tags and return plain text
                                return strip_tags($record->modul->isi);
                            })
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('file_lampiran')
                            ->label('File Lampiran')
                            ->content(function ($record) {
                                if (!$record || !$record->modul || empty($record->modul->file_path)) {
                                    return 'Tidak ada file lampiran';
                                }

                                $files = $record->modul->file_path;
                                if (is_array($files)) {
                                    return collect($files)->map(function ($file) {
                                        $filename = basename($file);
                                        return "📎 {$filename}";
                                    })->implode(', ');
                                }

                                $filename = basename($files);
                                return "📎 {$filename}";
                            })
                            ->columnSpanFull()
                            ->visible(fn($record) => $record && $record->modul && !empty($record->modul->file_path)),
                    ])
                    ->visible(fn($context) => $context === 'edit')
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('✏️ Jawaban Tugas')
                    ->schema([
                        Forms\Components\Textarea::make('isi_jawaban')
                            ->label('Jawaban')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull()
                            ->helperText('Tuliskan jawaban Anda dengan jelas dan lengkap'),

                        Forms\Components\FileUpload::make('url_file')
                            ->label('Upload File Pendukung')
                            ->multiple()
                            ->directory('jawaban-files')
                            ->acceptedFileTypes(['pdf', 'doc', 'docx', 'jpg', 'png', 'zip'])
                            ->maxSize(10240) // 10MB
                            ->columnSpanFull()
                            ->helperText('Maksimal 10MB per file. Format: PDF, DOC, DOCX, JPG, PNG, ZIP')
                            ->downloadable()
                            ->previewable(false),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Hidden::make('siswa_id')
                    ->default(Auth::id()),

                Forms\Components\Hidden::make('status')
                    ->default('draft'),
            ])
            ->extraAttributes([
                'class' => 'space-y-6'
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modul.mataPelajaran.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('modul.judul')
                    ->label('Tugas')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('modul.guru.nama')
                    ->label('Guru')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
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
                        'dikirim' => 'Sudah Dikirim',
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

                Tables\Columns\TextColumn::make('modul.deadline')
                    ->label('Deadline')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(function ($record) {
                        if (!$record->modul->deadline)
                            return null;
                        return $record->modul->deadline->isPast() ? 'danger' : 'success';
                    }),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Dikirim Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Belum dikirim'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mata_pelajaran')
                    ->relationship('modul.mataPelajaran', 'nama_mapel')
                    ->label('Mata Pelajaran'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => in_array($record->status, ['draft', 'dikirim']) && $record->status !== 'dinilai'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status === 'draft'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJawabans::route('/'),
            'create' => Pages\CreateJawaban::route('/create'),
            'view' => Pages\ViewJawaban::route('/{record}'),
            'edit' => Pages\EditJawaban::route('/{record}/edit'),
        ];
    }
}