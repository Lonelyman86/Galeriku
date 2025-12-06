<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FotoController extends Controller
{
    public function create ()
    {
        return view('pages.fotoaction.createfoto', [
            "title" => "Create New Post"
        ]);
    }

    public function index ()
    {
        // OPTIMASI: Select kolom spesifik
        $foto = Foto::with([
                'user:id,username,fullname,avatar', 
                'album:id,nama_album', 
                'like:id,foto_id,user_id', 
                'komentarfoto.user:id,username,fullname,avatar'
            ])
            ->latest()
            ->paginate(12);

        $albums = Album::select('id', 'nama_album')->get(); 

        return view('foto', [
            "title" => "foto",
            "foto" => $foto, 
            "albums" => $albums
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'lokasi_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'judul_foto' => 'required|string|max:255',
            'deskripsi_foto' => 'required|string',
            'album_id' => 'nullable',
        ]);

        if ($request->hasFile('lokasi_file')) {
            $file = $request->file('lokasi_file');
            $filename = time() . '_' . $file->hashName();

            // Simpan fisik file (Cara standar Laravel)
            $file->storeAs('public/foto', $filename);

            $foto = new Foto();
            $foto->judul_foto = $request->judul_foto;
            $foto->user_id = Auth::id();
            $foto->deskripsi_foto = $request->deskripsi_foto;
            $foto->lokasi_file = $filename;
            $foto->tanggal_unggah = now();
            // $foto->album_id = $request->album_id; // Opsional jika fitur pilih album aktif
            $foto->save();

            return redirect('studio')->with('success', 'Foto berhasil diunggah!');
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }

    public function updateAlbum(Request $request, $photoId)
    {
        // PERBAIKAN DI SINI:
        // Ubah 'required' jadi 'nullable' agar bisa menerima data kosong (untuk hapus dari album)
        $request->validate([
            'album_id' => 'nullable|exists:albums,id',
        ]);

        $foto = Foto::findOrFail($photoId);

        if (Auth::id() !== $foto->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin.');
        }

        $foto->album_id = $request->album_id;
        $foto->save();

        // Pesan notifikasi dinamis (sesuai aksi)
        $message = $request->album_id ? 'Foto berhasil ditambahkan ke album.' : 'Foto berhasil dikeluarkan dari album.';

        return redirect()->back()->with('success', $message);
    }

    public function destroy(Foto $photo)
    {
        if (Auth::id() !== $photo->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        if (Storage::disk('public')->exists('foto/' . $photo->lokasi_file)) {
            Storage::disk('public')->delete('foto/' . $photo->lokasi_file);
        }

        $photo->like()->delete();
        $photo->komentarfoto()->delete();
        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus!');
    }
}