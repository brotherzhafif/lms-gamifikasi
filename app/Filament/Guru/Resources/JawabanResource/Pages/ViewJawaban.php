<?php

namespace App\Filament\Guru\Resources\JawabanResource\Pages;

use App\Filament\Guru\Resources\JawabanResource;
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
