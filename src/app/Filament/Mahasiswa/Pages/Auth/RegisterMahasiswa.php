<?php

namespace App\Filament\Mahasiswa\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class RegisterMahasiswa extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nidn_nim')
                    ->label('NIM')
                    ->required()
                    ->maxLength(255),
                $this->getNameFormComponent()->label('Nama Lengkap Lengkap'),
                $this->getEmailFormComponent(),
                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y'),
                TextInput::make('semester')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(14),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                FileUpload::make('avatar_url')
                    ->label('Upload Foto Profil')
                    ->avatar()
                    ->directory('avatars')
                    ->image()
            ]);
    }

    // Mengubah isi data sesaat sebelum masuk ke database, agar Role otomatis Mahasiswa!
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'mahasiswa';
        $data['is_active'] = true;
        // Opsional: mengirim email perkenalan di sini

        return $data;
    }
}
