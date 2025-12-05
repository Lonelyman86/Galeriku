<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Foto;
use App\Models\Album; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($photoId)
    {
        $user = Auth::user();
        $photo = Foto::findOrFail($photoId); // Pakai findOrFail agar otomatis 404 jika tidak ada

        $existingLike = Like::where('user_id', $user->id)
                            ->where('foto_id', $photo->id)
                            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return redirect()->back()->with('danger', 'Photo unliked successfully');
        } else {
            Like::create([
                'user_id' => $user->id,
                'foto_id' => $photo->id,
            ]);
            return redirect()->back()->with('success', 'Photo liked successfully');
        }
    }

    public function likedPhotos()
    {
        $user = Auth::user();

        // OPTIMASI FINAL:
        // 1. paginate(12): Mencegah loading ribuan foto sekaligus.
        // 2. withCount('like'): Menghitung total like di database (jauh lebih cepat daripada menghitung array di PHP).
        $likedPhotos = Foto::where('status', 'approved')
            ->whereHas('like', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'album', 'komentarfoto.user']) 
            ->withCount('like') // Menyiapkan properti 'like_count'
            ->paginate(12); 

        $albums = Album::where('user_id', $user->id)->get();

        return view('layouts.liked', [
            'foto' => $likedPhotos,
            'albums' => $albums
        ]);
    }
}