<?php

namespace App\Filament\Dosen\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as FilamentLogin;
use Illuminate\Validation\ValidationException;

class CustomLogin extends FilamentLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('nidn_nim')
            ->label('NIDN')
            ->placeholder('Masukkan NIDN Dosen Anda')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'nidn_nim'  => $data['nidn_nim'],
            'password'  => $data['password'],
            'is_active' => 1,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.nidn_nim' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
