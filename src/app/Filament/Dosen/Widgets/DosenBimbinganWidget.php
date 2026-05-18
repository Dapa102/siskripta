<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Bimbingan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DosenBimbinganWidget extends BaseWidget
{
    protected static ?string $heading = 'Daftar Mahasiswa Bimbingan';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Filter anak bimbingan yang dosennya adalah user ini
                Bimbingan::where('dosen_id', auth()->id())->with('mahasiswa')
            )
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('mahasiswa.nidn_nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mahasiswa.email')
                    ->label('Email Kontak')
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
            ]);
    }
}