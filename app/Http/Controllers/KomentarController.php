<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, Foto $photo)
    {
        $request->validate([
            'isi_komentar' => 'required',
        ]);

        Komentar::create([
            'user_id' => Auth::id(),
            'foto_id' => $photo->id,
            'isi_komentar' => $request->input('isi_komentar'),
        ]);

        return back();
    }
}
