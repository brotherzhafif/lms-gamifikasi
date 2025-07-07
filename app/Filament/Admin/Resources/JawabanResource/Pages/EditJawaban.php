<?php

namespace App\Filament\Admin\Resources\JawabanResource\Pages;

use App\Filament\Admin\Resources\JawabanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJawaban extends EditRecord
{
    protected static string $resource = JawabanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
