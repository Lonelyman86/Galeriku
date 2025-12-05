<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, Foto $photo)
    {
        // OPTIMASI: Validasi lebih ketat
        $request->validate([
            'isi_komentar' => 'required|string|max:500', // Batasi 500 karakter biar aman
        ]);

        Komentar::create([
            'user_id' => Auth::id(),
            'foto_id' => $photo->id,
            'isi_komentar' => $request->input('isi_komentar'),
            // 'tanggal_komentar' => now(), // Tambahkan ini jika di migration ada kolomnya
        ]);

        return back()->with('success', 'Komentar terkirim!');
    }
}