<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Foto;
use App\Models\Album;

class HomeController extends Controller
{
    public function index(Request $request)
    {


        // Pakai Scope 'withCompleteDetails' yang kita buat di Model tadi
        $foto = Foto::withCompleteDetails()
                    ->where('status', 'approved')
                    ->latest()
                    ->paginate(20); // Ganti 20 sesuai keinginan

        if ($request->ajax()) {
            $view = view('partials.photo-grid', ['foto' => $foto])->render();
            return response()->json([
                'html' => $view,
                'data' => $foto->items(),
                'next_page_url' => $foto->nextPageUrl()
            ]);
        }

        // Album untuk keperluan lain (sidebar dll)
        $albums = Album::select('id', 'nama_album', 'user_id')->get();

        return view('layouts.home', [
            'title' => 'Home',
            'headerTitle' => 'Jelajahi',
            'foto' => $foto,
            'albums' => $albums
        ]);
    }

    public function StudioIndex(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Pakai Scope juga disini
            $foto = Foto::withCompleteDetails()
                        ->where('user_id', $user->id)
                        ->latest()
                        ->paginate(20);

            if ($request->ajax()) {
                $view = view('partials.studio-grid', ['foto' => $foto])->render();
                return response()->json([
                    'html' => $view,
                    'data' => $foto->items(),
                    'next_page_url' => $foto->nextPageUrl()
                ]);
            }

            $albums = Album::where('user_id', $user->id)->get();

            // --- ANALYTICS ---
            $totalPhotos = $foto->total(); // Karena pakai paginate, total() ambil jumlah semua record

            // Hitung Total Like (Semua foto user ini dapat berapa like)
            $totalLikes = $user->fotos()->withCount('like')->get()->sum('like_count');

            // Hitung Total Komentar (Semua foto user ini dapat berapa komentar)
            $totalComments = $user->fotos()->withCount('komentarfoto')->get()->sum('komentarfoto_count');

            return view('layouts.studio', [
                'title' => 'Studio',
                'foto' => $foto,
                'albums' => $albums,
                'totalPhotos' => $totalPhotos,
                'totalLikes' => $totalLikes,
                'totalComments' => $totalComments
            ]);
        } else {
            return redirect()->route('sign-in');
        }
    }

    public function likedPhotos()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Scope juga bisa dipakai lewat relasi
            $likedPhotos = $user->likedPhotos()
                ->withCompleteDetails()
                ->paginate(20);

            $totalLiked = $likedPhotos->total();

            $albums = Album::where('user_id', $user->id)->get();

            return view('layouts.liked', [
                'title' => 'Liked Photos',
                'foto' => $likedPhotos,
                'albums' => $albums,
                'totalLiked' => $totalLiked
            ]);
        } else {
            return redirect()->route('sign-in');
        }


    }

    public function followingFeed(Request $request) {
        if (!Auth::check()) return redirect()->route('sign-in');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil ID semua orang yang kita follow
        $followingIds = $user->following()->pluck('users.id');

        // Ambil detail orang yang difollow (untuk banner atas)
        $followingList = $user->following()->get();

        // Jika belum follow sesiapa pun, tampilkan view kosong/saran
        if($followingIds->isEmpty()) {
             // Bisa kita return view khusus, atau view home tapi kosong
             return view('layouts.following', [
                'title' => 'Mengikuti',
                'headerTitle' => 'Mengikuti',
                'foto' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'albums' => [],
                'isFollowingEmpty' => true,
                'followingList' => []
             ]);
        }

        // Ambil Foto dari User yang difollow
        $foto = Foto::withCompleteDetails()
                    ->whereIn('user_id', $followingIds)
                    ->where('status', 'approved')
                    ->latest()
                    ->paginate(20);

        if ($request->ajax()) {
            $view = view('partials.photo-grid', ['foto' => $foto])->render();
            return response()->json([
                'html' => $view,
                'data' => $foto->items(),
                'next_page_url' => $foto->nextPageUrl()
            ]);
        }

        $albums = Album::where('user_id', $user->id)->get();

        return view('layouts.following', [
            'title' => 'Mengikuti',
            'headerTitle' => 'Mengikuti',
            'foto' => $foto,
            'albums' => $albums,
            'followingList' => $followingList
        ]);
    }
}
