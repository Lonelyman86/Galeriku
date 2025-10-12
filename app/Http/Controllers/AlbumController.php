<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Foto;
use App\Models\Komentar;
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
        $foto = Foto::where('album_id', $album->id)->get();
        $komentar = Komentar::all();
        $albumOption = Album::where('user_id', Auth::id())->get();

        return view('ShowAlbum', [
            'title' => 'Album / ' . $album->nama_album,
            'album' => $album,
            'albumOption' => $albumOption,
            'foto' => $foto,
            'comments' => $komentar
        ]);
    }
}
