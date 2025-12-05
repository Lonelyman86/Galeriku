<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Foto;
use App\Models\Komentar; // Bisa dihapus jika tidak dipanggil lagi
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AlbumController extends Controller
{
    public function index()
    {
        return view('pages.albumaction.createalbum', [
            'title' => 'Create Album'
        ]);
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            'nama_album' => [
                'required',
                Rule::unique('albums')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
            'deskripsi' => 'required',
        ]);

        $v['user_id'] = Auth::id(); // Ambil ID user yang login
        Album::create($v);

        return redirect('/studio');
    }

    public function show(Album $album)
    {
        // OPTIMASI: 
        // 1. Eager load user, like, dan komentar agar query hemat.
        // 2. Gunakan paginate(12) supaya halaman album tidak berat jika isinya ratusan foto.
        $foto = Foto::with(['user', 'like', 'komentarfoto.user'])
                    ->where('album_id', $album->id)
                    ->latest()
                    ->paginate(12);

        // HAPUS INI: $komentar = Komentar::all(); (Sangat berat)

        // Dropdown untuk opsi pindah album
        $albumOption = Album::where('user_id', Auth::id())->get();

        return view('ShowAlbum', [
            'title' => 'Album / ' . $album->nama_album,
            'album' => $album,
            'albumOption' => $albumOption,
            'foto' => $foto,
            // 'comments' => $komentar // Tidak perlu dikirim
        ]);
    }

    public function destroy(Album $album)
    {
        // KEAMANAN: Cek apakah user yang login adalah pemilik album
        if (Auth::id() !== $album->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus album ini.');
        }

        // Opsi 1: lepas album dari foto (album_id jadi null)
        // Foto tidak terhapus, hanya keluar dari album.
        $album->foto()->update(['album_id' => null]);

        $album->delete();

        return redirect()->back()->with('success', 'Album berhasil dihapus.');
    }
}