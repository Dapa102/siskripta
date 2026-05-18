<?php
namespace App\Filament\Admin\Resources;

use App\Models\Sidang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SidangResource extends Resource
{
    protected static ?string $model = Sidang::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadwal Ujian Sidang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Informasi Pengajuan (Read Only)')
                    ->schema([
                        Forms\Components\Select::make('mahasiswa_id')->relationship('mahasiswa', 'name')->disabled(),
                        Forms\Components\TextInput::make('judul')->disabled()->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Atur Jadwal & Penguji')
                    ->schema([
                        Forms\Components\Select::make('dosen_penguji_id')
                            ->relationship('dosenPenguji', 'name', fn ($query) => $query->where('role', 'dosen'))
                            ->label('Dosen Penguji')
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('jadwal')->required(),
                        Forms\Components\TextInput::make('ruangan')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')->label('Mahasiswa')->searchable(),
                Tables\Columns\TextColumn::make('dosenPenguji.name')->label('Dosen Penguji')->placeholder('Belum di-set'),
                Tables\Columns\TextColumn::make('jadwal')->dateTime('d M Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('ruangan'),
                Tables\Columns\TextColumn::make('status_kelulusan')->badge()
                    ->colors(['warning' => 'menunggu', 'success' => 'lulus', 'primary' => 'lulus_bersyarat', 'danger' => 'tidak_lulus']),
            ])
            ->actions([ Tables\Actions\EditAction::make()->label('Atur Jadwal') ]);
    }
    public static function canCreate(): bool { return false; } // Admin tidak daftar, tapi menjadwalkan
    public static function getPages(): array { return [ 'index' => \App\Filament\Admin\Resources\SidangResource\Pages\ListSidangs::route('/'), 'edit' => \App\Filament\Admin\Resources\SidangResource\Pages\EditSidang::route('/{record}/edit'), ]; }
}