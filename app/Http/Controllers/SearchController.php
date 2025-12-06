<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Album;
use App\Models\User; // <-- tambahin kalau mau pakai

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back()->with('error', 'Masukkan kata kunci pencarian.');
        }

        // FOTO:
        // - judul_foto mengandung keyword
        // - ATAU user.fullname / username mengandung keyword
        $fotos = Foto::with(['user', 'album', 'like', 'komentarfoto.user'])
            ->where(function ($q) use ($query) {
                $q->where('judul_foto', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($uq) use ($query) {
                      $uq->where('fullname', 'like', "%{$query}%")
                         ->orWhere('username', 'like', "%{$query}%");
                  });
            })
            ->latest()
            ->take(50)
            ->get();

        // ALBUM masih sama
        $albums = Album::with('user')
            ->where('nama_album', 'like', "%{$query}%")
            ->latest()
            ->take(20)
            ->get();

        // (opsional) kalau mau sekalian tampilkan daftar user yang cocok
        $users = User::where('fullname', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->take(20)
            ->get();

        return view('search.results', [
            'query'  => $query,
            'fotos'  => $fotos,
            'albums' => $albums,
            'users'  => $users,   // <- kalau mau dipakai di blade
        ]);
    }
}
