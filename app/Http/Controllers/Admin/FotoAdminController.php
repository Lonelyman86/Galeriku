<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // WAJIB: Tambahkan ini untuk hapus file fisik

class FotoAdminController extends Controller
{
    public function index()
    {
        // OPTIMASI: Ganti get() dengan paginate(20)
        // Admin tidak perlu memuat 10.000 foto sekaligus, cukup 20 per halaman.
        // with('user', 'album') dipertahankan agar tidak N+1 Query.
        $fotos = Foto::with(['user', 'album'])
                     ->latest()
                     ->paginate(20); 

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