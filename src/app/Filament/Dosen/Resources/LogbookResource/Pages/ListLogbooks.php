<?php

namespace App\Filament\Dosen\Resources\LogbookResource\Pages;

use App\Filament\Dosen\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogbooks extends ListRecords
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
