# Laporan Awal Project Akhir (Siskripta)
**Bobot Laporan:** 40%
**Tech Stack:** Laravel 11, Filament v3, MariaDB, Docker.

## 1. Judul Project & Deskripsi Singkat
**Siskripta (Sistem Informasi Skripsi & Tugas Akhir Terpadu)**
Siskripta adalah sebuah portal akademik terintegrasi yang berfungsi mendigitalisasi proses penyusunan tugas akhir mahasiswa, dimulai dari tahap pemilihan dosen pembimbing, pengajuan judul, logbook bimbingan harian, hingga penilaian akhir ujian sidang kelulusan.

## 2. Analisis Masalah & Kebutuhan Sistem
**Latar Belakang:** 
Di banyak institusi ruang lingkup bimbingan masih dilakukan secara luring dan pemantauan dokumen terkesan berantakan sehingga mahasiswa kehilangan progress-nya. 
**Fitur Utama:**
- Multi-Panel Authentication (Admin, Dosen, Mahasiswa).
- Pemisahan track Skripsi dan Tugas Akhir.
- Review and Feedback judul berantai.
- Logbook pelacakan progress persentase.
- Modul pendaftaran dan grading sidang.

## 3. Arsitektur & Tech Stack
- **Framework Utama:** Laravel 11.
- **Admin/User Panel Framework:** Filament v3 (TALL Stack - Tailwind, Alpine.js, Laravel, Livewire).
- **Database:** MariaDB (dijalankan di atas environment Docker).
- **Notifications:** Laravel Database Notifications & Polling.

## 4. Rencana Perancangan Sistem

### A. Entity Relationship Diagram (ERD)
*(Diagram di bawah menggunakan standar Mermaid JS)*
```mermaid
erDiagram
    USERS {
        int id PK
        string name
        string email
        string role "super_admin, dosen, mahasiswa"
        boolean is_active
    }
    BIMBINGANS {
        int id PK
        int dosen_id FK
        int mahasiswa_id FK
    }
    SUBMISSIONS {
        int id PK
        int mahasiswa_id FK
        string jenis_pengajuan "skripsi/tugas_akhir"
        string judul
        string status "pending, acc, revisi, reject"
        int revisi_count
        int reject_count
    }
    LOGBOOKS {
        int id PK
        int mahasiswa_id FK
        string bab
        string status
        text catatan_dosen
    }
    SIDANGS {
        int id PK
        int mahasiswa_id FK
        int dosen_penguji_id FK
        datetime jadwal
        string status_kelulusan
        string nilai_huruf
    }

    USERS ||--|{ BIMBINGANS : "dosen memiliki"
    USERS ||--|{ BIMBINGANS : "mahasiswa dibimbing"
    USERS ||--|{ SUBMISSIONS : "mahasiswa mengajukan"
    USERS ||--|{ LOGBOOKS : "mahasiswa melapor"
    USERS ||--|{ SIDANGS : "mahasiswa diuji"
    USERS ||--|{ SIDANGS : "dosen menguji"