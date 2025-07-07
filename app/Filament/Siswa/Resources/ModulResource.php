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
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('is_active', true)
            ->where('kelas_id', $user->kelas_id);
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
                        'danger' => 'belum',
                        'warning' => 'draft',
                        'primary' => 'dikirim',
                        'secondary' => 'terlambat',
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
                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama_mapel')
                    ->label('Mata Pelajaran'),

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
                    ->visible(function ($record) {
                        if ($record->jenis !== 'tugas')
                            return false;

                        // Check if answer exists and its status
                        $jawaban = Jawaban::where('modul_id', $record->id)
                            ->where('siswa_id', Auth::id())
                            ->first();

                        // Only allow editing if no answer exists or if status is 'draft'
                        return !$jawaban || $jawaban->status === 'draft';
                    })
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

                Tables\Actions\Action::make('lihat_jawaban')
                    ->label('Lihat Jawaban')
                    ->icon('heroicon-o-eye')
                    ->visible(function ($record) {
                        if ($record->jenis !== 'tugas')
                            return false;

                        // Check if answer exists and is submitted or graded
                        $jawaban = Jawaban::where('modul_id', $record->id)
                            ->where('siswa_id', Auth::id())
                            ->first();

                        return $jawaban && in_array($jawaban->status, ['dikirim', 'terlambat', 'dinilai']);
                    })
                    ->action(function ($record) {
                        $jawaban = Jawaban::where('modul_id', $record->id)
                            ->where('siswa_id', Auth::id())
                            ->first();

                        return redirect()->to('/siswa/jawabans/' . $jawaban->id);
                    })
                    ->color('info'),

                // Remove tandai_selesai action from table - now only in detail page
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
