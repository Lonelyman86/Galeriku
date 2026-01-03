<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Album;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFotoRequest;

class FotoController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('photos.create', [
            "title" => "Create New Post",
            "categories" => $categories
        ]);
    }

    public function index()
    {
        // Mengambil foto dengan detail lengkap (user, album, category, likes, comments)
        $foto = Foto::withCompleteDetails()
            ->latest()
            ->paginate(12);

        $albums = Album::select('id', 'nama_album')
            ->where('user_id', Auth::id())
            ->get();

        return view('photos.index', [
            "title" => "foto",
            "foto" => $foto,
            "albums" => $albums
        ]);
    }

    public function upload(StoreFotoRequest $request)
    {
        // Validation handled by StoreFotoRequest

        if ($request->hasFile('lokasi_file')) {
            $file = $request->file('lokasi_file');
            $filename = time() . '_' . $file->hashName();

            // DETERMINE DISK & SAVE FILE
            if (app()->environment('production')) {
                // Use Cloudinary in Production
                $result = $file->storeOnCloudinary('foto');
                $filename = $result->getSecurePath(); // Save FULL URL
            } else {
                // Use Local Storage (Public) in Dev
                $filename = time() . '_' . $file->hashName();
                $file->storeAs('foto', $filename, 'public');
            }

            $foto = new Foto();
            $foto->judul_foto = $request->judul_foto;
            $foto->user_id = Auth::id();
            $foto->deskripsi_foto = $request->deskripsi_foto;
            $foto->lokasi_file = $filename;
            $foto->tanggal_unggah = now();
            // $foto->album_id = $request->album_id; 

            // 1. Simpan Category
            if ($request->filled('category_id')) {
                $foto->category_id = $request->category_id;
            }

            $foto->save();

            // 2. Simpan Tags
            if ($request->filled('tags')) {
                $this->syncTags($foto, $request->tags);
            }

            return redirect('studio')->with('success', 'Foto berhasil diunggah!');
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }

    public function updateAlbum(Request $request, $photoId)
    {
        $request->validate([
            'album_id' => 'nullable|exists:albums,id',
        ]);

        $foto = Foto::findOrFail($photoId);

        if (Auth::id() !== $foto->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin.');
        }

        $foto->album_id = $request->album_id;
        $foto->save();

        $message = $request->album_id ? 'Foto berhasil ditambahkan ke album.' : 'Foto berhasil dikeluarkan dari album.';

        return redirect()->back()->with('success', $message);
    }

    public function edit(Foto $photo)
    {
        if (Auth::id() !== $photo->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $categories = Category::all();
        return view('photos.edit', [
            "title" => "Edit Foto",
            "photo" => $photo,
            "categories" => $categories
        ]);
    }

    public function update(Request $request, Foto $photo)
    {
        if (Auth::id() !== $photo->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $request->validate([
            'judul_foto' => 'required|string|max:255',
            'deskripsi_foto' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $photo->judul_foto = $request->judul_foto;
        $photo->deskripsi_foto = $request->deskripsi_foto;

        if ($request->filled('category_id')) {
            $photo->category_id = $request->category_id;
        }

        // Tags Update
        if ($request->filled('tags')) {
            $this->syncTags($photo, $request->tags);
        }

        $photo->save();

        return redirect('/studio')->with('success', 'Foto berhasil diperbarui!');
    }

    public function destroy(Foto $photo)
    {
        if (Auth::id() !== $photo->user_id) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        // HAPUS FILE FISIK DARI STORAGE
        // Cek apakah file ada, lalu hapus
        if ($photo->lokasi_file && Storage::disk('public')->exists('foto/' . $photo->lokasi_file)) {
            Storage::disk('public')->delete('foto/' . $photo->lokasi_file);
        }

        // Hapus data terkait di database
        $photo->like()->delete();
        $photo->komentarfoto()->delete();
        $photo->tags()->detach(); // Lepas hubungan tag dulu
        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus!');
    }
    private function syncTags(Foto $foto, string $tagsInput)
    {
        $tagNames = explode(',', $tagsInput);
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $name = trim($tagName);
            if ($name) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
        }
        $foto->tags()->sync($tagIds);
    }
}
