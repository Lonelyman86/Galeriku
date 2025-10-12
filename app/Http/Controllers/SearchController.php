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

        // Cari berdasarkan judul foto dan nama album
        $fotos = Foto::where('judul_foto', 'like', "%{$query}%")->get();
        $albums = Album::where('nama_album', 'like', "%{$query}%")->get();

        return view('search.results', compact('query', 'fotos', 'albums'));
    }
}
