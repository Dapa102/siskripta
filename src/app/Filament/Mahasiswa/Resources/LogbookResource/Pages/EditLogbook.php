<?php

namespace App\Filament\Mahasiswa\Resources\LogbookResource\Pages;

use App\Filament\Mahasiswa\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogbook extends EditRecord
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
