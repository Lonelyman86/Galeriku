<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Komentar;
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
        // PERBAIKAN EFISIENSI (OPTIMASI):
        // 1. with(...): Mengambil data relasi (user, album, like, komentar) sekaligus di awal.
        // 2. latest(): Mengurutkan dari yang paling baru diupload.
        // 3. paginate(12): Membatasi hanya 12 foto per halaman (ganti angka sesuai kebutuhan).
        $foto = Foto::with(['user', 'album', 'like', 'komentarfoto.user'])
                    ->latest()
                    ->paginate(12);

        // HAPUS atau matikan baris ini karena boros memori:
        // $komentar = Komentar::all(); 

        // Album tetap diambil untuk keperluan dropdown modal (misal: "Masukan ke Album")
        $albums = Album::all(); 

        return view('foto', [
            "title" => "foto",
            "foto" => $foto, 
            // "comments" => $komentar, // Tidak perlu dikirim lagi
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
        ]);

        if ($request->hasFile('lokasi_file')) {
            $file = $request->file('lokasi_file');
            $filename = time() . '_' . $file->hashName();

            // Simpan fisik file
            $file->storeAs('public/foto', $filename);

            $foto = new Foto();
            $foto->judul_foto = $request->judul_foto;
            $foto->user_id = Auth::id();
            $foto->deskripsi_foto = $request->deskripsi_foto;
            $foto->lokasi_file = $filename;
            $foto->tanggal_unggah = now();
            // Jika Anda ingin user bisa langsung pilih album saat upload, aktifkan baris di bawah:
            // $foto->album_id = $request->album_id; 
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

        // Pastikan hanya pemilik foto yang bisa memindahkan album
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

        // Hapus file fisik gambar
        if (Storage::disk('public')->exists('foto/' . $photo->lokasi_file)) {
            Storage::disk('public')->delete('foto/' . $photo->lokasi_file);
        }

        // Hapus data relasi (cleanup database)
        $photo->like()->delete();
        $photo->komentarfoto()->delete();

        // Hapus record foto
        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus!');
    }
}