<?php

namespace App\Filament\Admin\Resources\ModulResource\Pages;

use App\Filament\Admin\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewModul extends ViewRecord
{
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
