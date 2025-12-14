<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\Foto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportAdminController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'foto'])
                         ->where('status', 'pending')
                         ->latest()
                         ->paginate(10);
                         
        return view('admin.reports.index', compact('reports'));
    }

    public function dismiss($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'dismissed']);
        
        return back()->with('success', 'Laporan diabaikan.');
    }

    public function ban(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $foto = $report->foto;

        if ($foto) {
             // Hapus file
             if ($foto->lokasi_file) {
                Storage::delete('public/foto/' . $foto->lokasi_file);
             }
             // Hapus record foto
             $foto->delete();
        }

        // Tandai laporan selesai
        $report->update(['status' => 'resolved']);
        
        // Opsional: Tandai semua laporan lain untuk foto yang sama sebagai resolved?
        // Report::where('foto_id', $report->foto_id)->update(['status' => 'resolved']);

        return back()->with('success', 'Foto telah dihapus dan laporan diselesaikan.');
    }
}
