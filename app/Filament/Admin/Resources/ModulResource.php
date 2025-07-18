<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ModulResource\Pages;
use App\Models\Modul;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModulResource extends Resource
{
    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Modul & Pembelajaran';

    protected static ?string $navigationLabel = 'Semua Modul';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('guru_id')
                    ->relationship('guru', 'nama')
                    ->required(),

                Forms\Components\Select::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama_mapel')
                    ->label('Mata Pelajaran')
                    ->required(),

                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Kelas')
                    ->required(),

                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),

                Forms\Components\RichEditor::make('isi')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                    ])
                    ->required(),

                Forms\Components\FileUpload::make('file_path')
                    ->label('File Lampiran')
                    ->multiple()
                    ->disk('public')
                    ->directory('modul-files')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'application/zip',
                        'text/plain'
                    ])
                    ->maxSize(10240) // 10MB
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()
                    ->helperText('File yang diizinkan: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG, ZIP, TXT (Maksimal 10MB per file)'),

                Forms\Components\DateTimePicker::make('deadline'),

                Forms\Components\TextInput::make('poin_reward')
                    ->numeric()
                    ->default(10)
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaran.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->searchable()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),

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
                    ]),

                Tables\Columns\TextColumn::make('poin_reward')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama_mapel')
                    ->label('Mata Pelajaran'),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Kelas'),

                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active'),

                Tables\Filters\SelectFilter::make('guru_id')
                    ->relationship('guru', 'nama')
                    ->label('Guru'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuls::route('/'),
            'create' => Pages\CreateModul::route('/create'),
            'view' => Pages\ViewModul::route('/{record}'),
            'edit' => Pages\EditModul::route('/{record}/edit'),
        ];
    }
}
