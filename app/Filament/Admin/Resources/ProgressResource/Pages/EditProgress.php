<?php

namespace App\Filament\Admin\Resources\ProgressResource\Pages;

use App\Filament\Admin\Resources\ProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgress extends EditRecord
{
    protected static string $resource = ProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
