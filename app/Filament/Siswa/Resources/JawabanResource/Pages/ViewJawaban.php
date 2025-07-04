<?php

namespace App\Filament\Siswa\Resources\JawabanResource\Pages;

use App\Filament\Siswa\Resources\JawabanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJawaban extends ViewRecord
{
    protected static string $resource = JawabanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
