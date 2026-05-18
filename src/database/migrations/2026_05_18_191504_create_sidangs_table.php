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
        Schema::create('sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            
            // opsional: untuk dosen penguji sidang
            $table->foreignId('dosen_penguji_id')->nullable()->constrained('users')->nullOnDelete(); 

            $table->enum('jenis_sidang', ['skripsi', 'tugas_akhir']);
            $table->string('judul');
            $table->string('file_laporan'); // File PDF Skripsi/TA yang sudah komplit
            
            // Penjadwalan
            $table->dateTime('jadwal')->nullable();
            $table->string('ruangan')->nullable();
            
            // Hasil Penilaian
            $table->enum('status_kelulusan', ['menunggu', 'lulus', 'lulus_bersyarat', 'tidak_lulus'])->default('menunggu');
            $table->string('nilai_huruf')->nullable(); // Misal: A, A-, B+
            $table->text('catatan_penguji')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidangs');
    }
};
