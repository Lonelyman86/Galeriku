<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Foto;
use App\Models\Komentar;
use App\Models\Album;

class HomeController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Gunakan 'with' untuk mengambil data User, Album, Like, dan Komentar sekaligus.
        // Ini mengubah ratusan query menjadi hanya 2-3 query (Eager Loading).
        $foto = Foto::with(['user', 'album', 'like', 'komentarfoto.user'])
                    ->where('status', 'approved')
                    ->latest() // Mengurutkan dari yang terbaru (opsional, tapi disarankan)
                    ->get();

        // PENYESUAIAN: Baris di bawah ini dimatikan karena komentar sudah diambil lewat $foto (di atas).
        // Mengambil Komentar::all() akan memakan banyak RAM jika komentar sudah ribuan.
        // $komentar = Komentar::all(); 

        $albums = Album::all();

        return view('layouts.home', [
            'title' => 'Home',
            'foto' => $foto,
            // 'comments' => $komentar, // Tidak perlu dikirim jika view menggunakan $item->komentarfoto
            'albums' => $albums
        ]);
    }

    public function StudioIndex()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // PERBAIKAN: Terapkan optimasi yang sama untuk Studio
            $foto = Foto::with(['user', 'album', 'like', 'komentarfoto.user'])
                        ->where('user_id', $user->id)
                        ->latest()
                        ->get();

            // $komentar = Komentar::all(); // Dimatikan demi performa
            
            $albums = Album::where('user_id', $user->id)->get();
            
            return view('layouts.studio', [
                'title' => 'Studio',
                'foto' => $foto,
                // 'comments' => $komentar,
                'albums' => $albums
            ]);
        } else {
            return redirect()->route('sign-in');
        }
    }

    public function likedPhotos()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Bagian ini sudah cukup baik karena sudah pakai 'with'
            $likedPhotos = $user->likedPhotos()->with(['user', 'album'])->get();
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