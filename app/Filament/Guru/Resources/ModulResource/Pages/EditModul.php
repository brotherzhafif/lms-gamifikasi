<?php

namespace App\Filament\Guru\Resources\ModulResource\Pages;

use App\Filament\Guru\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModul extends EditRecord
{
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
