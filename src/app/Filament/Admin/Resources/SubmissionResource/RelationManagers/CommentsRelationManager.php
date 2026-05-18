<?php

namespace App\Filament\Admin\Resources\SubmissionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Riwayat Komentar / Review';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('komentar')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('komentar')
                    ->label('Komentar')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}