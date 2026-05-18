<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    protected $guarded = [];

    public function dosen() {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa() {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
