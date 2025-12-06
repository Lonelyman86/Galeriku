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
        // Pakai Scope 'withCompleteDetails' yang kita buat di Model tadi
        $foto = Foto::withCompleteDetails()
                    ->where('status', 'approved')
                    ->latest()
                    ->paginate(20); // Ganti 20 sesuai keinginan

        // Album untuk keperluan lain (sidebar dll)
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
            
            // Pakai Scope juga disini
            $foto = Foto::withCompleteDetails()
                        ->where('user_id', $user->id)
                        ->latest()
                        ->paginate(20);
            
            $albums = Album::where('user_id', $user->id)->get();
            
            return view('layouts.studio', [
                'title' => 'Studio',
                'foto' => $foto,
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
            
            // Scope juga bisa dipakai lewat relasi
            $likedPhotos = $user->likedPhotos()
                ->withCompleteDetails()
                ->paginate(20);

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