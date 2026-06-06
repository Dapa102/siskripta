<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kelola Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'dosen' => 'Dosen',
                        'mahasiswa' => 'Mahasiswa',
                    ])
                    ->required()
                    ->live(), // <--- Jadikan reactive

                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    // field Nama hanya muncul setelah Role dipilih
                    ->visible(fn (\Filament\Forms\Get $get) => $get('role') !== null),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->visible(fn (\Filament\Forms\Get $get) => $get('role') !== null),

                Forms\Components\TextInput::make('nidn_nim')
                    // Label otomatis ganti menjadi NIDN atau NIM
                    ->label(fn (\Filament\Forms\Get $get) => $get('role') === 'dosen' ? 'NIDN' : 'NIM')
                    ->maxLength(255)
                    // HANYA muncul jika role yang dipilih adalah Dosen atau Mahasiswa
                    ->visible(fn (\Filament\Forms\Get $get) => in_array($get('role'), ['dosen', 'mahasiswa'])),

                Forms\Components\TextInput::make('bidang_keahlian')
                    ->label('Bidang Keahlian')
                    ->maxLength(255)
                    ->visible(fn (\Filament\Forms\Get $get) => $get('role') === 'dosen'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Password (Isi jika ingin ubah)')
                    ->visible(fn (\Filament\Forms\Get $get) => $get('role') !== null),

                 Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->live()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('role') !== 'super_admin'), // (Opsional) Biarkan admin selalu aktif

                Forms\Components\Textarea::make('nonactive_reason')
                    ->label('Alasan Penonaktifan')
                    ->placeholder('Misal: Anda belum melunasi UKT semester ini.')
                    // WAJIB ISI (dan muncul) HANYA JIKA is_active dimatikan (false)
                    ->required(fn (\Filament\Forms\Get $get) => $get('is_active') === false)
                    ->visible(fn (\Filament\Forms\Get $get) => $get('is_active') === false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nidn_nim')
                    ->label('NIDN / NIM')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bidang_keahlian')
                    ->label('Keahlian')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('mahasiswa.bimbinganMahasiswa.dosen.name')
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->visible(auth()->user()->role === 'super_admin'),

                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'super_admin',
                        'success' => 'dosen',
                        'primary' => 'mahasiswa',
                    ]),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'dosen' => 'Dosen',
                        'mahasiswa' => 'Mahasiswa',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
