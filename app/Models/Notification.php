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

    // ✅ UPDATE LOGIKA PESAN DISINI
    public function getMessageAttribute()
    {
        $actor = $this->actor;

        if (!$actor) {
            return 'Seseorang berinteraksi dengan postinganmu';
        }

        $displayName = $actor->fullname ?: $actor->username ?: 'Seseorang';

        switch ($this->type) {
            case 'like':
                return $displayName . ' menyukai fotomu';
            
            case 'comment':
                return $displayName . ' mengomentari postinganmu';

            // [BARU] Notifikasi Follow
            case 'follow':
                return $displayName . ' mulai mengikuti Anda';

            // [BARU] Notifikasi Postingan Baru
            case 'new_post':
                return $displayName . ' baru saja memposting foto baru';
            
            default:
                return 'Notifikasi baru';
        }
    }
}