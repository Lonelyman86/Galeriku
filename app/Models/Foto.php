<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'foto';

    // --- RELASI ---
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function komentarfoto()
    {
        return $this->hasMany(Komentar::class);
    }

    public function like()
    {
        return $this->hasMany(Like::class, 'foto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likefoto', 'foto_id', 'user_id')->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'foto_tag');
    }

    // --- [BARU] SCOPE QUERY EFISIEN ---
    // Gunanya: Biar di Controller gak perlu nulis 'with' panjang-panjang lagi
    public function scopeWithCompleteDetails($query)
    {
        return $query->with([
            'user:id,username,fullname,avatar',
            'album:id,nama_album',
            'like:id,foto_id,user_id',
            'komentarfoto.user:id,username,fullname,avatar',
            'category:id,name,slug',
            'tags:id,name,slug'
        ]);
    }
}