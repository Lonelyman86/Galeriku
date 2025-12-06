<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Foto;
use App\Models\Notification; // <--- WAJIB: Jangan lupa import model ini!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, Foto $photo)
    {
        // Validasi input
        $request->validate([
            'isi_komentar' => 'required|string|max:500', 
        ]);

        // 1. Simpan Komentar ke Database
        Komentar::create([
            'user_id' => Auth::id(),
            'foto_id' => $photo->id,
            'isi_komentar' => $request->input('isi_komentar'),
        ]);

        // 2. [BARU] Buat Notifikasi Komentar
        // Cek: Jangan kirim notifikasi jika user mengomentari fotonya sendiri
        if ($photo->user_id != Auth::id()) {
            Notification::create([
                'user_id'  => $photo->user_id, // Penerima (Pemilik Foto)
                'actor_id' => Auth::id(),      // Pengirim (Yang Komen)
                'type'     => 'comment',       // Tipe kita set 'comment' sesuai di Model tadi
                'data'     => [
                    'foto_id' => $photo->id,   // Simpan ID foto agar nanti bisa diklik
                ],
                'read_at'  => null,            // Status belum dibaca
            ]);
        }

        return back()->with('success', 'Komentar terkirim!');
    }
}