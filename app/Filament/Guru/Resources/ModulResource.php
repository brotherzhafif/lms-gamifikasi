<?php

namespace App\Filament\Guru\Resources;

use App\Filament\Guru\Resources\ModulResource\Pages;
use App\Models\Modul;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ModulResource extends Resource
{
    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Modul Saya';

    protected static ?string $navigationLabel = 'Kelola Modul';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('guru_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->multiple()
                    ->directory('modul-files')
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'png'])
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('deadline')
                    ->label('Deadline (Opsional)'),
                Forms\Components\TextInput::make('poin_reward')
                    ->numeric()
                    ->default(10)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Forms\Components\Hidden::make('guru_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('jenis')
                    ->colors([
                        'success' => 'materi',
                        'warning' => 'tugas',
                    ]),
                Tables\Columns\TextColumn::make('poin_reward')
                    ->label('Poin')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
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
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
