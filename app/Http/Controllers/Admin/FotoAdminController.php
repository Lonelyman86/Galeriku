<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Foto;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // WAJIB: Tambahkan ini untuk hapus file fisik

class FotoAdminController extends Controller
{
    public function index()
    {
        // Photos Pagination (default 'page')
        $fotos = Foto::with(['user', 'album'])
                     ->latest()
                     ->paginate(100, ['*'], 'page'); 
        
        // Reports Pagination (custom 'reports_page' to avoid conflict)
        $reports = Report::with(['user', 'foto'])
                         ->where('status', 'pending')
                         ->latest()
                         ->paginate(10, ['*'], 'reports_page');

        return view('admin.foto.index', compact('fotos', 'reports'));
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

        // OPTIMASI STORAGE: Hapus file fisik gambar dari folder 'public/foto'
        // Jika tidak dihapus, lama-lama harddisk server akan penuh dengan sampah.
        if ($foto->lokasi_file && Storage::disk('public')->exists('foto/' . $foto->lokasi_file)) {
            Storage::disk('public')->delete('foto/' . $foto->lokasi_file);
        }

        // Opsional: Hapus relasi (Like & Komentar) biar bersih total
        // (Tergantung settingan foreign key di database, tapi ini langkah aman)
        $foto->like()->delete();
        $foto->komentarfoto()->delete();

        $foto->delete();
        
        return back()->with('danger', 'Foto dan filenya berhasil dihapus permanen!');
    }
}