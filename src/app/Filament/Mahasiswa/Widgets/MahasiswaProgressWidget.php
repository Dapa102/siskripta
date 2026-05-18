<?php

namespace App\Filament\Mahasiswa\Widgets;

use App\Filament\Admin\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MahasiswaProgressWidget extends BaseWidget
{
    protected static ?string $heading = 'Progress Saya';
    protected int | string | array $columnSpan = 'full'; // Agar lebar/memanjang

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Cuma narik data milik mahasiswa yang lagi login
                Submission::where('mahasiswa_id', auth()->id())->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Pengajuan')
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'acc',
                        'danger' => 'reject',
                        'primary' => 'revisi',
                    ]),
                Tables\Columns\TextColumn::make('revisi_count')
                    ->label('Direvisi')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('reject_count')
                    ->label('Ditolak')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_seen_by_dosen')
                    ->label('Dibaca Dosen')
                   // --- Tambahkan logika penentuan centang otomatis ini: ---
                    ->getStateUsing(fn ($record) => $record->is_seen_by_dosen || $record->status !== 'pending')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle') // <-- Ikon centang hijau jika sudah dibaca / direview
                    ->falseIcon('heroicon-o-clock')       // <-- Ikon jam tangan jika masih pending sungguhan
                    ->trueColor('success')
                    ->falseColor('warning'),              // <-- Warna kuning agar tidak terlihat horor/error seperti merah
            ])

            ->actions([
                Tables\Actions\Action::make('lihat_detail')
                    ->label('Lihat & Baca Komentar')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Submission $record): string => SubmissionResource::getUrl('view', ['record' => $record->id]))
            ])
            ->paginated(false); // Matikan pagination karena data 1 mhs biasanya sedikit
    }
}