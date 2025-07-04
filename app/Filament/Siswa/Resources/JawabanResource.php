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

    protected static ?string $navigationGroup = 'Tugas & Kuis';

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
                    ->relationship('modul', 'judul', fn(Builder $query) => $query->where('is_active', true))
                    ->required()
                    ->disabled(fn($context) => $context === 'edit'),
                Forms\Components\Textarea::make('isi_jawaban')
                    ->label('Jawaban')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('url_file')
                    ->label('Upload File')
                    ->multiple()
                    ->directory('jawaban-files')
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'jpg', 'png'])
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Simpan sebagai Draft',
                        'dikirim' => 'Kirim Jawaban',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\Hidden::make('siswa_id')
                    ->default(Auth::id()),
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
                Tables\Columns\TextColumn::make('modul.guru.nama')
                    ->label('Guru')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
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
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                    ]),
                Tables\Filters\SelectFilter::make('modul.jenis')
                    ->label('Jenis Modul')
                    ->options([
                        'tugas' => 'Tugas',
                        'quiz' => 'Quiz',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => in_array($record->status, ['draft', 'dikirim'])),
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
