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
        'fullname',
        'email',
        'password',
        'google_id',
        'role_id', // WAJIB: Tambahkan ini agar bisa diisi saat seeding admin
        'avatar',
        'address',
        'bio', // Add bio
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role_id' => 'integer',
    ];

    // Relasi: User memiliki banyak foto
    public function fotos()
    {
        return $this->hasMany(Foto::class, 'user_id');
    }

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

    // 1. Relasi: Siapa saja yang mem-follow user ini (Pengikut)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    // 2. Relasi: Siapa saja yang di-follow oleh user ini (Mengikuti)
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    // 3. Helper: Cek apakah user ini sedang mem-follow user lain ($user)
    // Dipakai untuk menentukan tombol "Ikuti" atau "Mengikuti" yang muncul
    public function isFollowing(User $user)
    {
        return $this->following()->where('followed_id', $user->id)->exists();
    }
}