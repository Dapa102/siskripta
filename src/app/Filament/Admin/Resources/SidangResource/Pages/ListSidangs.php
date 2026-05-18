<?php

namespace App\Filament\Admin\Resources\SidangResource\Pages;

use App\Filament\Admin\Resources\SidangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSidangs extends ListRecords
{
    protected static string $resource = SidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
