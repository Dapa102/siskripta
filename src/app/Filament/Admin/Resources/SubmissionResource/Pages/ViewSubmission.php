<?php

namespace App\Filament\Admin\Resources\SubmissionResource\Pages;

use App\Filament\Admin\Resources\SubmissionResource;
// Tambahkan Baris Ini di atas:
use App\Filament\Admin\Resources\SubmissionResource\RelationManagers\CommentsRelationManager; 
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    // Tambahkan Kode Dibawah Ini:
    public function getRelationManagers(): array // Perbaikan: Gunakan getRelationManagers()
    {
        return [
            CommentsRelationManager::class,
        ];
    }
}