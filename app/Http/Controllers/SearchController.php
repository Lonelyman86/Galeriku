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
        $fotos = Foto::where('judul_foto', 'like', "%$query%")
                    ->orWhere('deskripsi_foto', 'like', "%$query%")
                    ->where('status', 'approved') // Hanya yang approved
                    ->with('user', 'like') // Eager load
                    ->latest()
                    ->get();

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

        return view('search.results', [
            'query' => $query,
            'fotos' => $fotos,
            'albums' => $albums,
            'users' => $users, // <--- Kirim data users ke view
        ]);
    }
}