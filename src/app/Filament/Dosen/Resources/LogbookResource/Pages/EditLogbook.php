<?php

namespace App\Filament\Dosen\Resources\LogbookResource\Pages;

use App\Filament\Dosen\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Mail\ReviewLogbookMail;
use Illuminate\Support\Facades\Mail;

class EditLogbook extends EditRecord
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Tambahkan method ini: Filament akan mengeksekusinya otomatis ketika dosen pencet 'Save changes'
    protected function afterSave(): void
    {
        // Ambil data logbook saat ini
        $logbook = $this->record;

        // Cek jika logbook mempunyai dosen yang sedang memberi catatan (Opsional tapi lebih aman)
        // Dan pastikan email mahasiswanya tidak null (tergantung skema relasi mahasiswanya seperti apa,
        // misal: logbook->mahasiswa->user->email)

        $emailTujuan = $logbook->mahasiswa->user->email ?? null;

        if ($emailTujuan) {
            Mail::to($emailTujuan)->send(new ReviewLogbookMail($logbook));
        }
    }
}
