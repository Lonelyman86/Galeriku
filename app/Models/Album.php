<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    // Gunakan hanya salah satu: ini lebih aman
    protected $fillable = ['nama_album', 'deskripsi', 'user_id', 'cover_image'];

    protected $table = 'albums';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foto()
    {
        return $this->hasMany(Foto::class);
    }
    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return asset('assets/img/album-placeholder.jpg'); // Pastikan ada placeholder
        }

        if (str_starts_with($this->cover_image, 'data:') || str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        return asset('storage/' . $this->cover_image);
    }
}
