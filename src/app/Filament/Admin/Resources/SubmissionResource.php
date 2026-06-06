<?php

namespace App\Filament\Admin\Resources;

use Filament\Notifications\Notification;
use App\Filament\Admin\Resources\SubmissionResource\RelationManagers\CommentsRelationManager;
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
    protected static ?string $pluralModelLabel = 'Pengajuan Skripsi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('mahasiswa_id')->default(fn() => auth()->id()),
                Forms\Components\Hidden::make('jenis_pengajuan')->default('skripsi'), // <-- Otomatis Skripsi

                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => auth()->user()->role === 'dosen'),

                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull()
                    ->disabled(fn () => auth()->user()->role === 'dosen'),

                Forms\Components\FileUpload::make('file_pendukung')
                    ->label('Upload File Proposal / Jurnal (Max. 10MB)')
                    ->directory('submissions')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->disabled(fn () => auth()->user()->role === 'dosen')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->visible(fn () => auth()->user()->role !== 'mahasiswa'),
                Tables\Columns\TextColumn::make('judul')->searchable()->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'acc',
                        'danger' => 'reject',
                        'primary' => 'revisi'
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->mutateRecordDataUsing(function (array $data): array {
                    if (auth()->user()->role === 'dosen') {
                        $submission = Submission::find($data['id']);
                        if ($submission && !$submission->is_seen_by_dosen && $submission->status === 'pending') {
                            $submission->update(['is_seen_by_dosen' => true]);
                        }
                    }
                    return $data;
                }),
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
                                'revisi' => 'Revisi'
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('komentar')->required(),
                    ])
                    ->action(function (Submission $record, array $data) {
                        if ($data['status'] === 'revisi') {
                            $record->increment('revisi_count');
                        } elseif ($data['status'] === 'reject') {
                            $record->increment('reject_count');
                        }

                        $record->update(['status' => $data['status']]);
                        $record->comments()->create(['user_id' => auth()->id(), 'komentar' => $data['komentar']]);

                        $title = match ($data['status']) {
                            'acc' => '🎉 Selamat! Pengajuan Anda di-ACC',
                            'revisi' => '⚠️ Pengajuan Butuh Revisi',
                            'reject' => '❌ Maaf, Pengajuan Ditolak',
                            default => 'Info Pengajuan'
                        };

                        Notification::make()
                            ->title($title)
                            ->body('Dosen pembimbing telah memberikan review & komentar baru pada pengajuan Skripsi Anda.')
                            ->icon('heroicon-o-information-circle')
                            ->iconColor('warning')
                            ->sendToDatabase($record->mahasiswa);
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()->role === 'mahasiswa' && in_array($record->status, ['pending', 'revisi'])),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // WAJIB: Hanya Skripsi!
        $query->where('jenis_pengajuan', 'skripsi');

        if ($user->role === 'dosen') {
            $mahasiswaIds = Bimbingan::where('dosen_id', $user->id)->pluck('mahasiswa_id');
            $query->whereIn('mahasiswa_id', $mahasiswaIds);
        } elseif ($user->role === 'mahasiswa') {
            $query->where('mahasiswa_id', $user->id);
        }

        return $query;
    }

    // --- Area ini ditata ulang syntax-nya ---

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'mahasiswa';
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'mahasiswa') return $record->mahasiswa_id === $user->id;
        if ($user->role === 'dosen') {
            return \App\Models\Bimbingan::where('dosen_id', $user->id)
                ->where('mahasiswa_id', $record->mahasiswa_id)
                ->exists();
        }
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if ($user->role === 'mahasiswa' && $record->mahasiswa_id === $user->id) {
            return in_array($record->status, ['pending', 'revisi', 'reject']);
        }
        return false;
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'view' => Pages\ViewSubmission::route('/{record}'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}
