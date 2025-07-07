<?php

namespace App\Filament\Admin\Resources\JawabanResource\Pages;

use App\Filament\Admin\Resources\JawabanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJawabans extends ListRecords
{
    protected static string $resource = JawabanResource::class;

    protected function getActions(): array
    {
        return [
            // No create action for admin - answers are created by students
        ];
    }
}
