<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Tampilkan halaman profil + form
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    // Update profil (nama, username, email, address, avatar, password opsional)
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'fullname' => ['required','string','max:100'],
            'username' => ['required','string','min:3','max:30', Rule::unique('users','username')->ignore($user->id)],
            'email'    => ['required','email','max:120', Rule::unique('users','email')->ignore($user->id)],
            'address'  => ['nullable','string','max:500'],
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], // 2MB
            // password opsional; kalau diisi, wajib konfirmasi
            'password' => ['nullable','confirmed','min:8'],
        ]);

        // Handle avatar baru (hapus lama jika ada)
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Password hanya di-update jika diisi
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}