<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Foto;
use App\Models\Album; 
use App\Models\Notification; // <--- WAJIB: Import Model Notification
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($photoId)
    {
        $user = Auth::user();
        $photo = Foto::findOrFail($photoId); 

        $existingLike = Like::where('user_id', $user->id)
                            ->where('foto_id', $photo->id)
                            ->first();

        if ($existingLike) {
            // Kalau sudah like, hapus like (Unlike)
            $existingLike->delete();
            return redirect()->back()->with('danger', 'Photo unliked successfully');
        } else {
            // Kalau belum, buat like baru
            Like::create([
                'user_id' => $user->id,
                'foto_id' => $photo->id,
            ]);

            // ✅ FITUR BARU: Buat Notifikasi Like
            // Cek: Jangan kirim notif jika me-like foto sendiri
            if ($photo->user_id != $user->id) {
                Notification::create([
                    'user_id'  => $photo->user_id, // Penerima (Pemilik Foto)
                    'actor_id' => $user->id,       // Pengirim (Yang Like)
                    'type'     => 'like',          // Tipe notifikasi
                    'data'     => [
                        'foto_id' => $photo->id,   // Simpan ID foto biar bisa diklik
                    ],
                    'read_at'  => null,
                ]);
            }

            return redirect()->back()->with('success', 'Photo liked successfully');
        }
    }

    public function likedPhotos()
    {
        $user = Auth::user();

        // Query yang sudah dioptimasi (tetap dipertahankan)
        $likedPhotos = Foto::where('status', 'approved')
            ->whereHas('like', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'user:id,username,fullname,avatar', // Load data user lengkap
                'album', 
                'komentarfoto.user'
            ]) 
            ->withCount('like') 
            ->paginate(12); 

        $albums = Album::where('user_id', $user->id)->get();

        return view('layouts.liked', [
            'foto' => $likedPhotos,
            'albums' => $albums
        ]);
    }
}