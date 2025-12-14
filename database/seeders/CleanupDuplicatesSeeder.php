<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Foto;

class CleanupDuplicatesSeeder extends Seeder
{
    public function run()
    {
        // Cari semua foto yang filenya diawali 'duplicate_'
        $fotos = Foto::where('lokasi_file', 'LIKE', 'duplicate_%')->get();

        if ($fotos->isEmpty()) {
            $this->command->info("Tidak ditemukan foto duplikat untuk dihapus.");
            return;
        }

        $count = 0;
        foreach ($fotos as $foto) {
            // Hapus file fisik
            if (Storage::disk('public')->exists('foto/' . $foto->lokasi_file)) {
                Storage::disk('public')->delete('foto/' . $foto->lokasi_file);
            }

            // Hapus record DB (soft delete atau hard delete tergantung model, di sini asumsi hard delete)
            $foto->forceDelete(); 
            $count++;
        }

        $this->command->info("Berhasil menghapus {$count} foto duplikat dan filenya.");
    }
}
