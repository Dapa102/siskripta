<?php

namespace App\Filament\Dosen\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Form;

class RegisterDosen extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nidn_nim')
                    ->label('NIDN')
                    ->required()
                    ->maxLength(255),
                $this->getNameFormComponent()->label('Nama Lengkap Lengkap'),
                $this->getEmailFormComponent(),
                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y'),
                // Dosen tidak butuh input semester, cukup bidang_keahlian
                TextInput::make('bidang_keahlian')
                    ->label('Bidang Keahlian')
                    ->required()
                    ->maxLength(255),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                FileUpload::make('avatar_url')
                    ->label('Upload Foto Profil')
                    ->avatar()
                    ->directory('avatars')
                    ->image()
            ]);
    }

    // Ubah Role Otomatis Jadi Dosen
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'dosen';
        $data['is_active'] = true;
        // Semester dikosongkan karena dia dosen
        $data['semester'] = null;

        return $data;
    }
}
