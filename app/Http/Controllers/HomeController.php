<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Foto;
use App\Models\Album;

class HomeController extends Controller
{
    public function index()
    {
        // OPTIMASI: 
        // 1. Ambil kolom spesifik saja (id, username, avatar) agar hemat bandwidth.
        // 2. Gunakan paginate(20) agar tidak meload ribuan foto sekaligus.
        $foto = Foto::with([
                'user:id,username,avatar', // Sesuaikan 'username' dgn 'name' jika perlu
                'album:id,nama_album',
                'like:id,foto_id,user_id',
                'komentarfoto:id,foto_id,user_id,isi_komentar', 
                'komentarfoto.user:id,username,avatar'
            ])
            ->where('status', 'approved')
            ->latest()
            ->paginate(20); // Menampilkan 20 foto per halaman

        // Album untuk sidebar/filter (ambil kolom yg perlu saja)
        $albums = Album::select('id', 'nama_album', 'user_id')->get();

        return view('layouts.home', [
            'title' => 'Home',
            'foto' => $foto,
            'albums' => $albums
        ]);
    }

    public function StudioIndex()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // OPTIMASI: Sama seperti index, gunakan pagination & select columns
            $foto = Foto::with([
                    'user:id,username,fullname,avatar',
                    'album:id,nama_album',
                    'like:id,foto_id,user_id',
                    'komentarfoto.user:id,username,fullname,avatar'
                ])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20);
            
            $albums = Album::where('user_id', $user->id)->get();
            $albumOption = Album::select('id', 'nama_album')
                            ->where('user_id', Auth::id())
                            ->get();
                    
            return view('layouts.studio', [
                'title' => 'Studio',
                'foto' => $foto,
                'albums' => $albums,
                'albumOption' => $albumOption
            ]);
        } else {
            return redirect()->route('sign-in');
        }
    }

    public function likedPhotos()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // OPTIMASI: Eager loading pada relasi likedPhotos
            $likedPhotos = $user->likedPhotos()
                ->with([
                    'user:id,username,avatar',
                    'album:id,nama_album'
                ])
                ->paginate(20); // Gunakan paginate juga disini

            $albums = Album::where('user_id', $user->id)->get();

            return view('layouts.liked', [
                'title' => 'Liked Photos',
                'foto' => $likedPhotos,
                'albums' => $albums
            ]);
        } else {
            return redirect()->route('sign-in');
        }
    }
}