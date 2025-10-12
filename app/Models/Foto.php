<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'foto';

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
}
