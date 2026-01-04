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
        $request->validate([
            'isi_komentar' => 'required|string|max:500',
        ]);

        // Cukup Create saja.
        // Notifikasi akan dibuat otomatis oleh Model Komentar (fungsi booted)
        Komentar::create([
            'user_id' => Auth::id(),
            'foto_id' => $photo->id,
            'isi_komentar' => $request->input('isi_komentar'),
        ]);

        return back()->with('success', 'Komentar terkirim!');
    }
    public function destroy(Komentar $komentar)
    {
        // Pastikan yang menghapus adalah pemilik komentar
        if (Auth::id() !== $komentar->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $komentar->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
