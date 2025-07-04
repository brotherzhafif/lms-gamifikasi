<?php

namespace App\Filament\Admin\Resources\ProgressResource\Pages;

use App\Filament\Admin\Resources\ProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgress extends ListRecords
{
    protected static string $resource = ProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
