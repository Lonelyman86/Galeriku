<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Tandai semua notifikasi user ini sebagai "sudah dibaca"
    public function readAll()
    {
        $user = Auth::user();

        // Update semua notif milik user ini yang masih unread
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }
}