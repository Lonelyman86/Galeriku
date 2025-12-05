<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Album;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back()->with('error', 'Masukkan kata kunci pencarian.');
        }

        // OPTIMASI: 
        // 1. Eager Loading (with): Ambil data user, album, like, & komentar sekaligus agar tidak query berulang.
        // 2. limit(50): Batasi hasil pencarian maksimal 50 foto agar server tidak berat.
        //    (Pencarian biasanya tidak perlu pagination yang kompleks, cukup batasi hasil teratas).
        
        $fotos = Foto::with(['user', 'album', 'like', 'komentarfoto.user'])
                    ->where('judul_foto', 'like', "%{$query}%")
                    ->latest()
                    ->take(50) // Ambil 50 foto terbaru yang cocok
                    ->get();

        $albums = Album::with('user') // Ambil data pemilik album juga
                    ->where('nama_album', 'like', "%{$query}%")
                    ->latest()
                    ->take(20) // Batasi 20 album
                    ->get();

        return view('search.results', compact('query', 'fotos', 'albums'));
    }
}