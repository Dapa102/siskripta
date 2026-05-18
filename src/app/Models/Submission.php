<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $guarded = [];
    
    public function mahasiswa() {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
