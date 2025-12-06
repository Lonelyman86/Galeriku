<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Album; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($photoId)
    {
        $user = Auth::user();
        $photo = Foto::findOrFail($photoId);

        // 1 Baris Ajaib: Otomatis Like/Unlike
        // Notifikasi juga otomatis dibuat oleh Model Like (fungsi booted) saat attached
        $status = $user->likedPhotos()->toggle($photo->id);

        // Cek hasil toggle
        if (count($status['attached']) > 0) {
            return back()->with('success', 'Photo liked successfully');
        }

        return back()->with('danger', 'Photo unliked successfully');
    }

    // Function likedPhotos bisa dihapus jika sudah tidak dipakai 
    // (karena biasanya sudah dihandle oleh HomeController@likedPhotos)
}