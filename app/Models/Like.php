<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'likefoto';

    // --- [BARU] EVENT OTOMATIS ---
    protected static function booted()
    {
        static::created(function ($like) {
            // Ambil foto untuk tahu pemiliknya
            $foto = $like->foto;

            // Cek: Jangan kirim notif jika like foto sendiri
            if ($foto && $foto->user_id != $like->user_id) {
                \App\Models\Notification::create([
                    'user_id'  => $foto->user_id, // Penerima
                    'actor_id' => $like->user_id, // Pengirim
                    'type'     => 'like',
                    'data'     => ['foto_id' => $foto->id],
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foto()
    {
        return $this->belongsTo(Foto::class, 'foto_id');
    }
}