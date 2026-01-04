<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Foto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;

class AlbumController extends Controller
{
    public function index()
    {
        return view('albums.create', [
            'title' => 'Create Album'
        ]);
    }

    public function store(StoreAlbumRequest $r)
    {
        $v = $r->validated();

        $v['user_id'] = Auth::id();
        Album::create($v);

        return redirect('/studio');
    }

    /**
     * Update nama & deskripsi album (fitur edit album).
     */
    public function update(UpdateAlbumRequest $request, Album $album)
    {
        // Authorization handled by UpdateAlbumRequest (authorize method)
        if (Auth::id() !== $album->user_id) {
             abort(403, 'Anda tidak memiliki izin.');
        }

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
             if ($album->cover_image) {
                 Storage::disk('public')->delete($album->cover_image);
             }
             $data['cover_image'] = $request->file('cover_image')->store('album-covers', 'public');
        }

        $album->update($data);

        return back()->with('success', 'Album berhasil diperbarui.');
    }

    public function show(Request $request, Album $album)
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

        if ($request->ajax()) {
            $view = view('partials.album-grid', ['foto' => $foto, 'album' => $album])->render();
            return response()->json([
                'html' => $view,
                'next_page_url' => $foto->nextPageUrl()
            ]);
        }

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

        return view('albums.show', [
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
