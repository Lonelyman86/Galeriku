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
        $reports = Report::with(['user', 'reportable'])
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
        $target = $report->reportable;

        $msg = 'Laporan diselesaikan.';

        if ($target) {
            if ($target instanceof \App\Models\Foto) {
                 if ($target->lokasi_file && Storage::exists('foto/' . $target->lokasi_file)) {
                    Storage::delete('foto/' . $target->lokasi_file);
                 }
                 $target->delete();
                 $msg = 'Foto berhasil dihapus.';
            } elseif ($target instanceof \App\Models\Komentar) {
                 $target->delete();
                 $msg = 'Komentar berhasil dihapus.';
            } elseif ($target instanceof \App\Models\User) {
                 // Implementasi Ban User belum ada, sementara hanya resolve report
                 $msg = 'Laporan user diselesaikan (User tidak dihapus).';
            }
        }

        $report->update(['status' => 'resolved']);

        return back()->with('success', $msg);
    }
}
