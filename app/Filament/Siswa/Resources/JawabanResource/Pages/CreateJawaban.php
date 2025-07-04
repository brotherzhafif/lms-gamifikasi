<?php

namespace App\Filament\Siswa\Resources\JawabanResource\Pages;

use App\Filament\Siswa\Resources\JawabanResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions;

class CreateJawaban extends CreateRecord
{
    protected static string $resource = JawabanResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return Actions\Action::make('create')
            ->label('💾 Simpan sebagai Draft')
            ->color('warning')
            ->action('create');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return Actions\Action::make('kirim')
            ->label('📤 Kirim Jawaban')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Kirim Jawaban')
            ->modalSubheading('Pastikan jawaban Anda sudah lengkap sebelum mengirim.')
            ->action(function () {
                $data = $this->form->getState();

                // Tentukan status berdasarkan deadline
                $modul = \App\Models\Modul::find($data['modul_id']);
                $status = 'dikirim';
                if ($modul->deadline && now()->isAfter($modul->deadline)) {
                    $status = 'terlambat';
                }

                $data['status'] = $status;
                $data['submitted_at'] = now();

                $record = static::getModel()::create($data);

                $message = $status === 'terlambat'
                    ? 'Jawaban berhasil dikirim, namun melewati deadline.'
                    : 'Jawaban berhasil dikirim!';

                Notification::make()
                    ->title($message)
                    ->body('Guru akan segera memeriksa jawaban Anda.')
                    ->success()
                    ->send();

                return redirect($this->getResource()::getUrl('index'));
            });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'draft';
        return $data;
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return Notification::make()
            ->title('Draft berhasil disimpan!')
            ->body('Jawaban Anda telah disimpan sebagai draft.')
            ->success();
    }
}
