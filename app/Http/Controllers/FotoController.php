<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Komentar;
use App\Models\Album;
use Illuminate\Support\Facades\Storage; // Pastikan ini diimpor
use Illuminate\Support\Facades\Auth; // Pastikan ini diimpor untuk Auth::id()

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
        $foto = Foto::all();
        $komentar = Komentar::all();
        $albums = Album::all();
        return view('foto', [
            "title" => "foto",
            "foto" => $foto ,
            "comments" => $komentar,
            "albums" => $albums
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'lokasi_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul_foto' => 'required|string|max:255',
            'deskripsi_foto' => 'required|string',
            'album_id' => 'nullable',
            // 'user_id' => 'required' // Baris ini dihapus/dikomentari karena user_id akan diambil dari Auth::id()
        ]);

        if ($request->hasFile('lokasi_file')) {
            $file = $request->file('lokasi_file');
            $filename = time() . '_' . $file->hashName();

            // --- PERBAIKAN PENTING DI SINI ---
            // Simpan file menggunakan Storage Facade Laravel ke disk 'public'
            // Ini akan menyimpannya di storage/app/public/foto/
            $file->storeAs('public/foto', $filename);
            // --- AKHIR PERBAIKAN ---

            $foto = new Foto();
            $foto->judul_foto = $request->judul_foto;
            $foto->user_id = Auth::id(); // Ambil ID user yang sedang login
            $foto->deskripsi_foto = $request->deskripsi_foto;
            $foto->lokasi_file = $filename;
            $foto->tanggal_unggah = now();
            // $foto->album_id = $request->album_id; // Tambahkan ini jika album_id perlu disimpan saat upload awal
            $foto->save();

            return redirect('studio')->with('success', 'Foto berhasil diunggah!');
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }

    public function updateAlbum(Request $request, $photoId)
    {
        $request->validate([
            'album_id' => 'required|exists:albums,id',
        ]);

        $foto = Foto::findOrFail($photoId);

        if (Auth::id() !== $foto->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah album foto ini.');
        }

        $foto->album_id = $request->album_id;
        $foto->save();

        return redirect()->back()->with('success', 'Foto berhasil ditambahkan ke album.');
    }

    public function destroy(Foto $photo)
    {
        if (Auth::id() !== $photo->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus foto ini.');
        }

        if (Storage::disk('public')->exists('foto/' . $photo->lokasi_file)) {
            Storage::disk('public')->delete('foto/' . $photo->lokasi_file);
        }

 // Hapus semua likes terkait foto ini
 $photo->like()->delete();

 // Hapus semua komentar terkait foto ini (kalau perlu)
 $photo->komentarfoto()->delete();


        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus!');
    }
}