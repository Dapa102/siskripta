<?php

namespace App\Filament\Dosen\Resources\SidangResource\Pages;

use App\Filament\Dosen\Resources\SidangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSidang extends EditRecord
{
    protected static string $resource = SidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
