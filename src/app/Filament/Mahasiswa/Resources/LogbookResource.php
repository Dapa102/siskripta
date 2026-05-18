<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\LogbookResource\Pages;
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
    protected static ?string $navigationLabel = 'Logbook Bimbingan';
    protected static ?string $pluralModelLabel = 'Logbook';

    // Membatasi data HANYA untuk login Mahasiswa itu sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('mahasiswa_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('mahasiswa_id')
                    ->default(fn () => auth()->id()),
                
                Forms\Components\TextInput::make('bab')
                    ->label('Bagian / Bab')
                    ->placeholder('Contoh: Bab 1, Bab 2, dst')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('judul_pembahasan')
                    ->label('Topik / Pembahasan')
                    ->placeholder('Contoh: Latar Belakang dan Rumusan Masalah')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->label('Detail Bimbingan / Pertanyaan ke Dosen')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\FileUpload::make('file_progress')
                    ->label('Lampiran Dokumen (PDF/Word/ZIP)')
                    ->directory('logbooks')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'])
                    ->maxSize(10240) 
                    ->columnSpanFull(),

                Forms\Components\Fieldset::make('Review & Feedback Dosen (Isi Bagian Ini)')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending/Menunggu',
                                'revisi' => 'Ada Revisi',
                                'disetujui' => 'Disetujui',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('catatan_dosen')
                            ->label('Catatan dari Dosen Pembimbing')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (?Logbook $record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bab')
                    ->label('Bab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul_pembahasan')
                    ->label('Materi')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'revisi' => 'danger',
                        'disetujui' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Lapor')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogbooks::route('/'),
            'create' => Pages\CreateLogbook::route('/create'),
            'edit' => Pages\EditLogbook::route('/{record}/edit'),
        ];
    }
}