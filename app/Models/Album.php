<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    // Gunakan hanya salah satu: ini lebih aman
    protected $fillable = ['nama_album', 'deskripsi', 'user_id'];

    protected $table = 'albums';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foto()
    {
        return $this->hasMany(Foto::class);
    }
}
