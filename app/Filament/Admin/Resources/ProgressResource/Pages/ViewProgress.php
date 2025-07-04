<?php

namespace App\Filament\Admin\Resources\ProgressResource\Pages;

use App\Filament\Admin\Resources\ProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProgress extends ViewRecord
{
    protected static string $resource = ProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
