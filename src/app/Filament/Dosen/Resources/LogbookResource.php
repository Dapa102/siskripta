<?php

namespace App\Filament\Dosen\Resources;

use App\Filament\Dosen\Resources\LogbookResource\Pages;
use App\Models\Logbook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogbookResource extends Resource
{
    protected static ?string $model = Logbook::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Review Logbook';
    protected static ?string $pluralModelLabel = 'Logbook Mahasiswa';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('mahasiswa', function ($query) {
            $query->whereHas('bimbinganMahasiswa', function ($q) {
                $q->where('dosen_id', auth()->id());
            });
        });
    }

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Laporan Mahasiswa')
                    ->schema([
                        Forms\Components\TextInput::make('bab')->label('Bagian / Bab')->disabled(),
                        Forms\Components\TextInput::make('judul_pembahasan')->label('Topik')->disabled(),
                        Forms\Components\Textarea::make('keterangan')->label('Detail')->disabled()->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Review & Feedback Dosen')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(['pending' => 'Pending/Menunggu', 'revisi' => 'Ada Revisi', 'disetujui' => 'Disetujui'])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_dosen')->label('Komentar')->required()->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')->label('Nama Mahasiswa')->searchable(),
                Tables\Columns\TextColumn::make('bab')->label('Bab'),
                Tables\Columns\TextColumn::make('judul_pembahasan')->label('Materi')->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) { 'pending' => 'warning', 'revisi' => 'danger', 'disetujui' => 'success', default => 'gray' }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Review Laporan')->icon('heroicon-o-check-badge'),
                Tables\Actions\Action::make('download')
                    ->label('Lihat File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Logbook $record): string => $record->file_progress ? asset('storage/' . $record->file_progress) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Logbook $record): bool => $record->file_progress !== null)
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogbooks::route('/'),
            'edit' => Pages\EditLogbook::route('/{record}/edit'),
        ];
    }
}