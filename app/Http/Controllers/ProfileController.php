<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Foto;
use App\Models\Album;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'fullname' => ['required','string','max:100'],
            'username' => ['required','string','min:3','max:30', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['required','email','max:120', Rule::unique('users','email')->ignore($user->id)],
            'bio'      => ['nullable','string','max:1000'], // Add bio
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'password' => ['nullable','confirmed','min:8'],
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->getRealPath();
            $type = $file->getClientOriginalExtension();
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $validated['avatar'] = $base64;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // Function ini yang dipanggil oleh Route::get('/user/{id}', ...)
    public function showPublicProfile(Request $request, $id)
    {
        // 1. Cari User (Jika tidak ketemu, otomatis 404)
        $user = User::findOrFail($id);

        // 2. Ambil Foto Milik User (Hanya yang Approved)
        $foto = Foto::where('user_id', $id)
                    ->where('status', 'approved') // WAJIB: Jangan tampilkan foto pending/rejected
                    ->with(['user', 'like', 'komentarfoto.user', 'album']) // Eager loading biar cepat
                    ->latest()
                    ->paginate(12); // Pagination biar halaman gak berat kalau fotonya ribuan

        if ($request->ajax()) {
            $view = view('partials.public-profile-grid', ['foto' => $foto, 'user' => $user])->render();
            return response()->json([
                'html' => $view,
                'next_page_url' => $foto->nextPageUrl()
            ]);
        }

        // 3. Ambil Album Milik User
        $albums = Album::where('user_id', $id)->get();

        // 4. Return ke View
        // Pastikan nama file kamu di folder: resources/views/profile/show.blade.php
        return view('profile.public', compact('user', 'foto', 'albums'));
    }
}
