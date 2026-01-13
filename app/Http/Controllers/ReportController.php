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
            'reportable_id' => 'required|numeric',
            'reportable_type' => 'required|string|in:foto,user,komentar',
            'reason' => 'required|string|max:255',
        ]);

        $typeMap = [
            'foto' => \App\Models\Foto::class,
            'user' => \App\Models\User::class,
            'komentar' => \App\Models\Komentar::class,
        ];

        // Explicitly map class name to prevent arbitrary class instantiation
        $modelClass = $typeMap[$request->reportable_type];

        Report::create([
            'user_id' => Auth::id(),
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $modelClass,
            'reason' => $request->reason,
        ]);

        if($request->ajax()){
           return response()->json(['message' => 'Laporan berhasil dikirim.']);
        }

        return back()->with('success', 'Laporan berhasil dikirim. Terima kasih atas bantuan Anda.');
    }
}
