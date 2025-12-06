<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'fullname',
        'address',
        'avatar',
        'role_id', // WAJIB: Tambahkan ini agar bisa diisi saat seeding admin
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role_id' => 'integer', // Casting ke integer biar aman
    ];

    // Relasi ke Foto yang disukai
    public function likedPhotos()
    {
        return $this->belongsToMany(Foto::class, 'likefoto', 'user_id', 'foto_id')->withTimestamps();
    }

    // Accessor untuk URL Avatar
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar)
            : asset('assets/img/default-profile.png'); // SESUAIKAN path default image
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    // HELPER ADMIN (Clean Code)
    public function isAdmin()
    {
        // Asumsi: 1 adalah kode untuk Admin
        return $this->role_id === 1;
    }
}