<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Foto;
use App\Models\Album;
use App\Models\Komentar;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total data dasar
        // (Ini query ringan, jadi dipisah tidak masalah demi keterbacaan)
        $totalUser = User::count();
        $totalAlbum = Album::count();
        $totalFoto = Foto::count();
        $totalKomentar = Komentar::count();

        // Hitung manual satu per satu agar lebih aman di berbagai environment database
        // (Performance impact sangat kecil untuk data < 1 juta)
        $fotoPending = Foto::where('status', 'pending')->count();
        $fotoApproved = Foto::where('status', 'approved')->count();
        $fotoRejected = Foto::where('status', 'rejected')->count();

        // Data buat chart
        $chartData = [
            'labels' => ['Pending', 'Approved', 'Rejected'],
            'data' => [$fotoPending, $fotoApproved, $fotoRejected],
        ];

        return view('admin.dashboard', compact(
            'totalUser', 'totalAlbum', 'totalFoto', 'totalKomentar',
            'fotoPending', 'fotoApproved', 'fotoRejected', 'chartData'
        ));
    }
}