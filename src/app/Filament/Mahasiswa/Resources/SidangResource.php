<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\SidangResource\Pages;
use App\Models\Sidang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SidangResource extends Resource
{
    protected static ?string $model = Sidang::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Pendaftaran Sidang';
    protected static ?string $pluralModelLabel = 'Jadwal Sidang Saya';

    public static function getEloquentQuery(): Builder
    {
        // Mahasiswa hanya bisa melihat jadwal sidang miliknya sendiri
        return parent::getEloquentQuery()->where('mahasiswa_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('mahasiswa_id')->default(fn () => auth()->id()),
                
                Forms\Components\Fieldset::make('Formulir Pendaftaran Sidang')
                    ->schema([
                        Forms\Components\Select::make('jenis_sidang')
                            ->options([
                                'skripsi' => 'Sidang Skripsi',
                                'tugas_akhir' => 'Sidang Tugas Akhir',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Final (yang di-ACC)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('file_laporan')
                            ->label('Upload File Laporan Lengkap (PDF) max 15MB')
                            ->directory('laporan_sidang')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(15360) // Maks 15MB
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // Bagian detail ini hanya akan muncul setelah admin mengatur jadwalnya (Read Only bagi Mahasiswa)
                Forms\Components\Fieldset::make('Informasi Pelaksanaan & Hasil')
                    ->schema([
                        Forms\Components\DateTimePicker::make('jadwal')->disabled(),
                        Forms\Components\TextInput::make('ruangan')->disabled(),
                        Forms\Components\TextInput::make('nilai_huruf')->disabled(),
                        Forms\Components\TextInput::make('status_kelulusan')->disabled(),
                        Forms\Components\Textarea::make('catatan_penguji')->disabled()->columnSpanFull(),
                    ])
                    ->visible(fn (?Sidang $record) => $record !== null), // Muncul hanya saat ngedit/view
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis_sidang')
                    ->badge()
                    ->colors(['primary' => 'skripsi', 'success' => 'tugas_akhir']),
                Tables\Columns\TextColumn::make('judul')->wrap(),
                
                Tables\Columns\TextColumn::make('jadwal')
                    ->label('Waktu Sidang')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Menunggu Jadwal Ditetapkan'),

                Tables\Columns\TextColumn::make('ruangan')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status_kelulusan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'lulus' => 'success',
                        'lulus_bersyarat' => 'primary',
                        'tidak_lulus' => 'danger',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Lihat Detail / Edit')
                    // Hanya bisa mengedit selagi statusnya masih 'Menunggu'
                    ->visible(fn (Sidang $record) => $record->status_kelulusan === 'menunggu'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSidangs::route('/'),
            'create' => Pages\CreateSidang::route('/create'),
            'edit' => Pages\EditSidang::route('/{record}/edit'),
        ];
    }
}