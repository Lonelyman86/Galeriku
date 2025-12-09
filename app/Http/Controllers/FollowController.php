<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification; // <--- PENTING: Import Model Notification
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    // Fungsi untuk Follow atau Unfollow (Toggle)
    public function toggle(User $user)
    {
        $currentUser = Auth::user();

        // Mencegah user mem-follow dirinya sendiri
        if ($currentUser->id === $user->id) {
            return back();
        }

        // Fitur Toggle:
        // toggle() mengembalikan array berisi ID yang di-attach (follow) dan di-detach (unfollow)
        // Kita simpan hasilnya ke variabel $changes
        $changes = $currentUser->following()->toggle($user->id);

        // Cek apakah hasilnya "attached" (artinya baru saja Follow)
        // Jika iya, kirim notifikasi
        if (!empty($changes['attached'])) {
            Notification::create([
                'user_id'  => $user->id,        // Penerima (Orang yang difollow)
                'actor_id' => $currentUser->id, // Pengirim (Yang memfollow)
                'type'     => 'follow',         // Tipe notifikasi
                'data'     => [],               // Kosongkan saja untuk follow
                'read_at'  => null,
            ]);
        }

        return back();
    }
}