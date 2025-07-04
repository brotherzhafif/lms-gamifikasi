<?php

namespace App\Filament\Guru\Resources\ModulResource\Pages;

use App\Filament\Guru\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModuls extends ListRecords
{
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
