<?php

namespace App\Filament\Admin\Resources\JawabanResource\Pages;

use App\Filament\Admin\Resources\JawabanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJawaban extends ViewRecord
{
    protected static string $resource = JawabanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
