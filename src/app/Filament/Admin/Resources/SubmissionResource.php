<?php

namespace App\Filament\Admin\Resources; // Sesuaikan namespace jika foldernya berbeda (misal: App\Filament\Resources)

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
                    ->label('Upload File Proposal / Jurnal (Max. 10MB)') // <--- Tambahkan Baris Label di Sini
                    ->directory('submissions')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240) // Ini yang barusan kita bahas jika butuh limit 10MB
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
                        // 1. Catat Counter Jika Dosen Pilih Revisi/Tolak
                        if ($data['status'] === 'revisi') {
                            $record->increment('revisi_count');
                        } elseif ($data['status'] === 'reject') {
                            $record->increment('reject_count');
                        }

                        // 2. Update Status Pengajuan
                        $record->update(['status' => $data['status']]);

                        // 3. Simpan Pesan/Komentar Dosen
                        $record->comments()->create([
                            'user_id' => auth()->id(),
                            'komentar' => $data['komentar']
                        ]);
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()->role === 'mahasiswa' && in_array($record->status, ['pending', 'revisi'])),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // 100% memaksa Filament menghapus menu ini dari sidebar untuk Admin
        return auth()->user()->role !== 'super_admin';
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
            'view' => Pages\ViewSubmission::route('/{record}'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya yang return true (dosen/mahasiswa) yang bisa lihat menunya di sidebar
        return auth()->user()->role !== 'super_admin';
    }

    public static function canCreate(): bool
    {
        // HANYA Mahasiswa yang boleh membuat pengajuan baru
        return auth()->user()->role === 'mahasiswa';
    }

    public static function getRelations(): array
    {
        return [
             CommentsRelationManager::class,
        ];
    }

    // IZIN MELIHAT DETAIL (VIEW)
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') return true;
        
        // Cek: Apakah ini pengajuannya si mahasiswa ini sendiri?
        if ($user->role === 'mahasiswa') return $record->mahasiswa_id === $user->id;
        
        // Cek: Apakah ini bimbingannya si dosen ini?
        if ($user->role === 'dosen') {
            $bimbingan = \App\Models\Bimbingan::where('dosen_id', $user->id)
                            ->where('mahasiswa_id', $record->mahasiswa_id)
                            ->exists();
            return $bimbingan;
        }

        return false;
    }

    // IZIN MENGUBAH / REVISI / EDIT
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        // HANYA mahasiswa sendiri yang boleh nge-edit dan itupun hanya saat statusnya Pending, Revisi, atau Reject. (Dosen sama sekali tidak boleh ngedit isi tulisan mhs)
        if ($user->role === 'mahasiswa' && $record->mahasiswa_id === $user->id) {
            return in_array($record->status, ['pending', 'revisi', 'reject']);
        }
        
        return false;
    }

    // IZIN MENGHAPUS (Hanya Admin yang boleh hapus mutlak)
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }
}