<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // WAJIB: Import Hash

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
            'address'  => ['nullable','string','max:500'],
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'password' => ['nullable','confirmed','min:8'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // PERBAIKAN: Enkripsi password sebelum update
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}