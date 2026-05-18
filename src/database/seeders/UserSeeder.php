<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // <-- Wajib dipanggil untuk nge-daftarin pekerjaan

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 0. DAFTARKAN SEMUA ROLE KE SISTEM DULU AGAR TIDAK ERROR
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'dosen']);
        Role::firstOrCreate(['name' => 'mahasiswa']);

        // 1. Buat Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin', 
                'password' => Hash::make('password'),
                'role' => 'super_admin', 
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        // 2. Buat Dosen
        $dosen = User::firstOrCreate(
            ['email' => 'dosen@test.com'],
            [
                'name' => 'Dosen',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'is_active' => true,
            ]
        );
        $dosen->assignRole('dosen'); // Sekarang ini aman

        // 3. Buat Mahasiswa
        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Mahasiswa',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'is_active' => true,
            ]
        );
        $mahasiswa->assignRole('mahasiswa'); // Sekarang ini juga aman
    }
}