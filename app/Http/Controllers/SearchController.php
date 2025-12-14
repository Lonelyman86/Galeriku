<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Album;
use App\Models\User; // <--- Import Model User
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        // 1. Cari Foto
        $fotosQuery = Foto::where('status', 'approved')
                        ->withCompleteDetails() // Gunakan Scope biar lengkap
                        ->latest();

        if ($request->has('tag')) {
            $tag = $request->input('tag');
            $fotosQuery->whereHas('tags', function($q) use ($tag) {
                $q->where('name', $tag)->orWhere('slug', $tag);
            });
        } elseif ($request->has('category')) {
            $cat = $request->input('category');
            $fotosQuery->whereHas('category', function($q) use ($cat){
                $q->where('name', $cat)->orWhere('slug', $cat);
            });
        } else {
            // Default Search (Judul/Deskripsi/Kategori/Tags)
            $fotosQuery->where(function($q) use ($query){
                $q->where('judul_foto', 'like', "%$query%")
                  ->orWhere('deskripsi_foto', 'like', "%$query%")
                  ->orWhereHas('category', function($q) use ($query) {
                      $q->where('name', 'like', "%$query%");
                  })
                  ->orWhereHas('tags', function($q) use ($query) {
                      $q->where('name', 'like', "%$query%");
                  });
            });
        }
        
        $fotos = $fotosQuery->get();

        // 2. Cari Album
        $albums = Album::where('nama_album', 'like', "%$query%")
                       ->with('user')
                       ->latest()
                       ->get();

        // 3. [BARU] Cari User
        // Kita cari berdasarkan username ATAU fullname
        $usersQuery = User::where(function($q) use ($query) {
                            $q->where('username', 'like', "%$query%")
                              ->orWhere('fullname', 'like', "%$query%");
                        });

        // Exclude (kecualikan) diri sendiri jika sedang login
        if (Auth::check()) {
            $usersQuery->where('id', '!=', Auth::id());
        }

        $users = $usersQuery->get();

        // Tentukan Label Pencarian
        $label = 'Semua';
        if($query) $label = '"'.$query.'"';
        if($request->has('tag')) $label = '#'.$request->input('tag');
        if($request->has('category')) $label = 'Kategori: '.$request->input('category');

        return view('search.results', [
            'query' => $label, // Override query with label for display
            'fotos' => $fotos,
            'albums' => $albums,
            'users' => $users,
        ]);
    }
}