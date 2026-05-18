<?php

namespace App\Filament\Admin\Resources; // Sesuaikan namespace jika foldernya berbeda (misal: App\Filament\Resources)

use App\Filament\Admin\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use App\Models\Bimbingan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pengajuan Skripsi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('mahasiswa_id')
                    ->default(auth()->id()),
                
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn ($record) => auth()->user()->role === 'dosen'),

                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull()
                    ->disabled(fn ($record) => auth()->user()->role === 'dosen'),

                Forms\Components\FileUpload::make('file_pendukung')
                    ->directory('submissions')
                    ->acceptedFileTypes(['application/pdf'])
                    ->disabled(fn ($record) => auth()->user()->role === 'dosen'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->visible(auth()->user()->role !== 'mahasiswa'),
                
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'acc',
                        'danger' => 'reject',
                        'primary' => 'revisi',
                    ]),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Logika Dosen membaca submission: ubah flag seen
                        if (auth()->user()->role === 'dosen') {
                            $submission = Submission::find($data['id']);
                            if (!$submission->is_seen_by_dosen && $submission->status === 'pending') {
                                $submission->update(['is_seen_by_dosen' => true]);
                            }
                        }
                        return $data;
                    }),

                // Tombol Review hanya untuk Dosen
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => auth()->user()->role === 'dosen')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'acc' => 'Terima (ACC)',
                                'reject' => 'Tolak',
                                'revisi' => 'Revisi',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('komentar')
                            ->required(),
                    ])
                    ->action(function (Submission $record, array $data) {
                        // Update status submission
                        $record->update(['status' => $data['status']]);

                        // Simpan komentar
                        $record->comments()->create([
                            'user_id' => auth()->id(),
                            'komentar' => $data['komentar']
                        ]);
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()->role === 'mahasiswa' && in_array($record->status, ['pending', 'revisi'])),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Jika Dosen: Hanya tampilkan mahasiswa bimbingannya
        if ($user->role === 'dosen') {
            $mahasiswaIds = Bimbingan::where('dosen_id', $user->id)->pluck('mahasiswa_id');
            $query->whereIn('mahasiswa_id', $mahasiswaIds);
        }
        
        // 2. Jika Mahasiswa: Hanya tampilkan miliknya sendiri
        elseif ($user->role === 'mahasiswa') {
            $query->where('mahasiswa_id', $user->id);
        }

        // 3. Super admin melihat semuanya otomatis
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}