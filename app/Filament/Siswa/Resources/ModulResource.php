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
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($record) => $record->deadline && $record->deadline->isPast() ? 'danger' : null)
                    ->placeholder('Tidak ada deadline'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->jenis === 'materi') {
                            // Untuk materi, cek apakah sudah ada progress
                            $progress = Progress::where('user_id', Auth::id())
                                ->where('modul_id', $record->id)
                                ->exists();
                            return $progress ? 'selesai' : 'belum';
                        } else {
                            // Untuk tugas, cek jawaban
                            $jawaban = Jawaban::where('siswa_id', Auth::id())
                                ->where('modul_id', $record->id)
                                ->first();

                            if (!$jawaban) {
                                return 'belum';
                            }

                            return $jawaban->status;
                        }
                    })
                    ->colors([
                        'secondary' => 'belum',
                        'warning' => 'draft',
                        'primary' => 'dikirim',
                        'danger' => 'terlambat',
                        'success' => ['dinilai', 'selesai'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'belum' => 'Belum Dikerjakan',
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                        'selesai' => 'Selesai',
                        default => 'Belum Dikerjakan'
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pengerjaan')
                    ->options([
                        'belum' => 'Belum Dikerjakan',
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'terlambat' => 'Terlambat',
                        'dinilai' => 'Sudah Dinilai',
                        'selesai' => 'Selesai',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value'])
                            return $query;

                        $status = $data['value'];
                        $userId = Auth::id();

                        if ($status === 'selesai') {
                            // Filter untuk materi yang sudah selesai
                            return $query->where('jenis', 'materi')
                                ->whereHas('progresses', function ($q) use ($userId) {
                                $q->where('user_id', $userId);
                            });
                        } elseif ($status === 'belum') {
                            // Filter untuk yang belum dikerjakan sama sekali
                            return $query->where(function ($q) use ($userId) {
                                $q->where('jenis', 'materi')
                                    ->whereDoesntHave('progresses', function ($subQ) use ($userId) {
                                        $subQ->where('user_id', $userId);
                                    });
                            })->orWhere(function ($q) use ($userId) {
                                $q->where('jenis', 'tugas')
                                    ->whereDoesntHave('jawabans', function ($subQ) use ($userId) {
                                        $subQ->where('siswa_id', $userId);
                                    });
                            });
                        } else {
                            // Filter untuk status jawaban tugas
                            return $query->where('jenis', 'tugas')
                                ->whereHas('jawabans', function ($q) use ($userId, $status) {
                                $q->where('siswa_id', $userId)
                                    ->where('status', $status);
                            });
                        }
                    }),
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
                    ->color('primary'),

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
