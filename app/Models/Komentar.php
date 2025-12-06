<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'komentarfoto';

    // --- [BARU] EVENT OTOMATIS ---
    // Setiap kali komentar berhasil dibuat, fungsi ini jalan otomatis
    protected static function booted()
    {
        static::created(function ($komentar) {
            // Ambil data foto terkait untuk tahu siapa pemiliknya
            $foto = $komentar->foto;

            // Cek: Jangan kirim notif jika mengomentari foto sendiri
            if ($foto && $foto->user_id != $komentar->user_id) {
                \App\Models\Notification::create([
                    'user_id'  => $foto->user_id, // Penerima (Pemilik Foto)
                    'actor_id' => $komentar->user_id, // Pengirim (Komentator)
                    'type'     => 'comment',
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