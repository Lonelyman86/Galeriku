<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Foto;
use Illuminate\Http\Request;

class FotoAdminController extends Controller
{
    public function index()
    {
        $fotos = Foto::with('user', 'album')->latest()->get();
        return view('admin.foto.index', compact('fotos'));
    }

    public function approve($id)
    {
        $foto = Foto::findOrFail($id);
        $foto->update(['status' => 'approved']);
        return back()->with('success', 'Foto disetujui!');
    }

public function reject(Request $request, $id)
{
    $request->validate([
        'note' => 'required|string|max:255',
    ]);

    $foto = Foto::findOrFail($id);
    $foto->update([
        'status' => 'rejected',
        'note' => $request->note,
    ]);

    return back()->with('warning', 'Foto ditolak dengan alasan: ' . $request->note);
}

    public function destroy($id)
    {
        $foto = Foto::findOrFail($id);
        $foto->delete();
        return back()->with('danger', 'Foto dihapus permanen!');
    }
}
