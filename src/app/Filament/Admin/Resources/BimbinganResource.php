<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BimbinganResource\Pages;
use App\Filament\Admin\Resources\BimbinganResource\RelationManagers;
use App\Models\Bimbingan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BimbinganResource extends Resource
{
    protected static ?string $model = Bimbingan::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Assign Pembimbing';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dosen_id')
                    ->label('Dosen Pembimbing')
                    ->options(User::where('role', 'dosen')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->options(User::where('role', 'mahasiswa')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->unique(ignoreRecord: true), // 1 mhs hanya 1 pembimbing
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dosen.name')
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mahasiswa.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBimbingans::route('/'),
            'create' => Pages\CreateBimbingan::route('/create'),
            'edit' => Pages\EditBimbingan::route('/{record}/edit'),
        ];
    }
}
