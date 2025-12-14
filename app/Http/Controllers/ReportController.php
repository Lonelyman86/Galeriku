<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'foto_id' => 'required|exists:foto,id',
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'foto_id' => $request->foto_id,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Laporan berhasil dikirim. Terima kasih atas bantuan Anda.');
    }
}
