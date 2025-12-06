<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // ✅ LOGIKA PESAN NOTIFIKASI
    public function getMessageAttribute()
    {
        $actor = $this->actor;

        // Jika user pengirim sudah dihapus
        if (!$actor) {
            return 'Seseorang berinteraksi dengan postinganmu';
        }

        // Prioritas nama: Fullname > Username > "Seseorang"
        $displayName = $actor->fullname ?: $actor->username ?: 'Seseorang';

        switch ($this->type) {
            case 'like':
                return $displayName . ' menyukai fotomu';
            
            // ✅ PERUBAHAN DI SINI (Ganti 'follow' jadi 'comment')
            case 'comment':
                return $displayName . ' mengomentari postinganmu';
            
            default:
                return 'Notifikasi baru';
        }
    }
}