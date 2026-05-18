<?php

namespace App\Filament\Admin\Resources\SubmissionResource\Pages;

use App\Filament\Admin\Resources\SubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubmission extends EditRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // LOGIKA SAKTI: Tiap kali Mahasiswa Klik Save/Simpan
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Reset otomatis statusnya menjadi 'pending' (Menunggu Dosen)
        $data['status'] = 'pending';
        
        // 2. Reset mata Dosen (seolah ini adalah lembaran baru yang belum dibaca)
        $data['is_seen_by_dosen'] = false;

        return $data;
    }
    
    // (Opsional) Langsung larikan ke halaman depan setelah selesai edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}