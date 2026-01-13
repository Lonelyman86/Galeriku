<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function index()
    {
        return view('layouts.sign-up', [
            'title' => 'Sign-up',
            'active' => 'sign-up'
        ]);
    }

    public function store(Request $request)
    {
        // OPTIMASI VALIDASI:
        // 1. Hapus 'name' jika di database Anda menggunakan 'fullname'.
        // 2. Hapus 'max:15' pada password agar user bisa bikin password panjang (lebih aman).
        // 3. Ubah password min:7 jadi min:8 (standar minimal saat ini).

        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100', // Sesuaikan max dengan ProfileController
            // 'name' => 'required|min:3|max:10', // HAPUS INI jika tidak ada kolom 'name' di DB
            'username' => ['required', 'min:3', 'max:30', 'unique:users'], // Disamakan dengan ProfileController
            'email'    => 'required|email:dns|unique:users',
            'password' => 'required|min:8' // Jangan pakai max untuk password!
        ]);

        // Enkripsi Password (Sudah Benar)
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Jika Anda menghapus validasi 'name' di atas, baris ini aman.
        // Tapi jika form HTML masih mengirim input 'name' dan Anda ingin mengabaikannya saat save ke DB:
        // unset($validatedData['name']);

        // Tetapkan role default (User Biasa = 2)
        $validatedData['role_id'] = 2;

        User::create($validatedData);

        return redirect('/sign-in')->with('success', 'Registration successful! Welcome to the club!');
    }
}
