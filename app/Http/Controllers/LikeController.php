<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Fungsi untuk like/unlike foto
    public function toggle($photoId)
    {
        $user = Auth::user();
        $photo = Foto::find($photoId);

        if (!$user || !$photo) {
            // Tampilkan halaman 404 jika user atau foto tidak ditemukan
            abort(404, 'Photo or user not found');
        }

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

    // Fungsi untuk menampilkan semua foto yang disukai oleh user
    public function likedPhotos()
{
    $user = Auth::user();

    $likedPhotos = Foto::whereHas('like', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->with(['user', 'album', 'komentarfoto.user', 'like'])->get();

    $albums = \App\Models\Album::where('user_id', $user->id)->get(); // ⬅️ TAMBAHKAN INI

    return view('layouts.liked', [
        'foto' => $likedPhotos,
        'albums' => $albums // ⬅️ DAN INI
    ]);
}
}
