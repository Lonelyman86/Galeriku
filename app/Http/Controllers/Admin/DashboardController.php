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

        // OPTIMASI: Hitung 3 status foto dalam 1 kali query database (Single Query)
        // Daripada melakukan 3x query count() terpisah, kita minta database menghitungnya sekaligus.
        // Ini jauh lebih efisien.
        $statusCounts = Foto::selectRaw("
            count(case when status = 'pending' then 1 end) as pending,
            count(case when status = 'approved' then 1 end) as approved,
            count(case when status = 'rejected' then 1 end) as rejected
        ")->first();

        // Ambil hasilnya dari object query di atas
        $fotoPending = $statusCounts->pending;
        $fotoApproved = $statusCounts->approved;
        $fotoRejected = $statusCounts->rejected;

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