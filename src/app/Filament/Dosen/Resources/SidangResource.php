<?php
namespace App\Filament\Dosen\Resources;

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
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Penilaian Sidang';

    public static function getEloquentQuery(): Builder
    {
        // Dosen hanya melihat daftar sidang di mana ia menjadi PENGUJI (bukan pembimbing)
        return parent::getEloquentQuery()->where('dosen_penguji_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Form Penilaian Kelulusan')
                    ->schema([
                        Forms\Components\Select::make('nilai_huruf')
                            ->options(['A' => 'A (Sangat Baik)', 'A-' => 'A-', 'B+' => 'B+', 'B' => 'B (Baik)', 'C' => 'C (Cukup)', 'D' => 'D (Mengulang)'])
                            ->required(),
                        Forms\Components\Select::make('status_kelulusan')
                            ->options(['lulus' => 'Lulus', 'lulus_bersyarat' => 'Lulus Bersyarat', 'tidak_lulus' => 'Tidak Lulus'])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_penguji')->label('Catatan Revisi Sidang')->required()->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')->label('Mahasiswa'),
                Tables\Columns\TextColumn::make('judul')->limit(30),
                Tables\Columns\TextColumn::make('jadwal')->dateTime('d M Y, H:i'),
                Tables\Columns\TextColumn::make('ruangan'),
                Tables\Columns\TextColumn::make('status_kelulusan')->badge()->colors(['warning' => 'menunggu', 'success' => 'lulus', 'danger' => 'tidak_lulus']),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download PDF Laporan')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Sidang $record): string => $record->file_laporan ? asset('storage/' . $record->file_laporan) : '#')
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->label('Input Nilai & Status'),
            ]);
    }
    public static function canCreate(): bool { return false; }
    public static function getPages(): array { return [ 'index' => \App\Filament\Dosen\Resources\SidangResource\Pages\ListSidangs::route('/'), 'edit' => \App\Filament\Dosen\Resources\SidangResource\Pages\EditSidang::route('/{record}/edit'), ]; }
}