<?php

namespace App\Filament\Siswa\Resources;

use App\Filament\Siswa\Resources\ModulResource\Pages;
use App\Models\Modul;
use App\Models\Progress;
use App\Models\Jawaban;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
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
                    ->visible(fn($record) => $record->jenis === 'tugas')
                    ->action(function ($record) {
                        // Check if answer already exists
                        $existingAnswer = Jawaban::where('modul_id', $record->id)
                            ->where('siswa_id', Auth::id())
                            ->first();

                        if ($existingAnswer) {
                            // Redirect to edit existing answer
                            return redirect()->to('/siswa/jawabans/' . $existingAnswer->id . '/edit');
                        } else {
                            // Create new answer
                            $newAnswer = Jawaban::create([
                                'modul_id' => $record->id,
                                'siswa_id' => Auth::id(),
                                'status' => 'draft',
                            ]);
                            return redirect()->to('/siswa/jawabans/' . $newAnswer->id . '/edit');
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn($record) => "Kerjakan {$record->jenis}: {$record->judul}")
                    ->modalSubheading('Anda akan diarahkan ke halaman pengerjaan'),
                Tables\Actions\Action::make('tandai_selesai')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record->jenis === 'materi' && !Progress::where('user_id', Auth::id())->where('modul_id', $record->id)->exists())
                    ->action(function ($record) {
                        Progress::create([
                            'user_id' => Auth::id(),
                            'modul_id' => $record->id,
                            'jumlah_poin' => $record->poin_reward,
                            'jenis_aktivitas' => 'selesai_materi',
                            'keterangan' => "Menyelesaikan materi: {$record->judul}",
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Materi Selesai')
                    ->modalSubheading(fn($record) => "Anda akan mendapat {$record->poin_reward} poin")
                    ->color('success'),
            ]);
    }

    // Add form method to support view page
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->disabled(),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                    ])
                    ->disabled(),
                Forms\Components\RichEditor::make('isi')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuls::route('/'),
            'view' => Pages\ViewModul::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
