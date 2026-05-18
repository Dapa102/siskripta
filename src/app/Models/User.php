<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'nidn_nim',
        'bidang_keahlian',
        'avatar_url',
        'is_active',
        'nonactive_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        } else {
            $hash = md5(strtolower(trim($this->email)));

            return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp&r=g&s=250';
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Jika panelnya 'admin', HANYA Super Admin yang boleh akses
        if ($panel->getId() === 'admin') {
            return $this->role === 'super_admin';
        }

        // 2. Jika panelnya 'dosen', HANYA Dosen yang boleh akses
        if ($panel->getId() === 'dosen') {
            return $this->role === 'dosen';
        }

        // 3. Jika panelnya 'mahasiswa', HANYA Mahasiswa yang boleh akses
        if ($panel->getId() === 'mahasiswa') {
            return $this->role === 'mahasiswa';
        }

        return false; // Default: tolak akses ke panel-panel lain yang tidak dikenal
    }

    public function bimbinganDosen() {
        return $this->hasMany(Bimbingan::class, 'dosen_id');
    }

    public function bimbinganMahasiswa() {
        return $this->hasOne(Bimbingan::class, 'mahasiswa_id');
    }

    public function submissions() {
        return $this->hasMany(Submission::class, 'mahasiswa_id');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'mahasiswa_id');
    }
}
