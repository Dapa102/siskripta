<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
             $table->id();
            // Cukup hubungkan dengan mahasiswa (Dosen otomatis diketahui dari tabel bimbingans)
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('bab'); // Contoh: "Bab 1", "Bab 2"
            $table->string('judul_pembahasan');
            $table->text('keterangan');
            $table->string('file_progress')->nullable(); // Untuk upload lampiran Word/PDF
            
            // Status dan balasan dari dosen
            $table->enum('status', ['pending', 'revisi', 'disetujui'])->default('pending');
            $table->text('catatan_dosen')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
