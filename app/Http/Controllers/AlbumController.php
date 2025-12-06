<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Foto;
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

        $v['user_id'] = Auth::id();
        Album::create($v);

        return redirect('/studio');
    }

    /**
     * Update nama & deskripsi album (fitur edit album).
     */
    public function update(Request $request, Album $album)
    {
        // Pastikan yang ngedit memang pemilik albumnya
        if (Auth::id() !== $album->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $data = $request->validate([
            'nama_album' => [
                'required',
                Rule::unique('albums')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($album->id), // supaya nama lama sendiri nggak dianggap bentrok
            ],
            'deskripsi' => 'required',
        ]);

        $album->update($data);

        return back()->with('success', 'Album berhasil diperbarui.');
    }

    public function show(Album $album)
    {
        // 1. Ambil foto yang SUDAH ada di album ini
        $foto = Foto::with([
                'user:id,username,fullname,avatar',
                'like:id,foto_id,user_id',
                'komentarfoto.user:id,username,fullname,avatar'
            ])
            ->where('album_id', $album->id)
            ->latest()
            ->paginate(12);

        // 2. Ambil foto milik user yang TIDAK berada di album ini (untuk "Ambil dari Galeri")
        $fotoTersedia = Foto::select('id', 'judul_foto', 'lokasi_file')
            ->where('user_id', Auth::id())
            ->where(function($q) use ($album) {
                $q->where('album_id', '!=', $album->id)
                  ->orWhereNull('album_id');
            })
            ->latest()
            ->get();

        // 3. Dropdown pilihan album (kalau mau dipakai di view)
        $albumOption = Album::select('id', 'nama_album')
            ->where('user_id', Auth::id())
            ->get();

        return view('ShowAlbum', [
            'title'        => 'Album / ' . $album->nama_album,
            'album'        => $album,
            'albumOption'  => $albumOption,
            'foto'         => $foto,
            'fotoTersedia' => $fotoTersedia,
        ]);
    }

    // 4. Function untuk memproses "Ambil Foto"
    public function addExistingPhotos(Request $request, Album $album)
    {
        // Pastikan juga cuma pemilik album yang bisa nambah
        if (Auth::id() !== $album->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $request->validate([
            'foto_ids' => 'required|array',
        ]);

        Foto::whereIn('id', $request->foto_ids)
            ->where('user_id', Auth::id())
            ->update(['album_id' => $album->id]);

        return redirect()->back()->with('success', 'Foto berhasil ditambahkan ke album!');
    }

    public function destroy(Album $album)
    {
        if (Auth::id() !== $album->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        // Lepas album_id dari foto (set null) sebelum hapus album
        $album->foto()->update(['album_id' => null]);
        $album->delete();

        return redirect()->back()->with('success', 'Album berhasil dihapus.');
    }
}
