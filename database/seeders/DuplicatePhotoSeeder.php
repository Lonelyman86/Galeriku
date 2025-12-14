<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Foto;
use App\Models\User;
use App\Models\Category;

class DuplicatePhotoSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil list semua file yang ada di storage
        $files = Storage::disk('public')->files('foto');
        
        // Filter hanya file gambar
        $images = array_filter($files, function($file) {
            return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
        });

        if (empty($images)) {
            $this->command->error("Tidak ada foto di storage/app/public/foto untuk diduplikasi.");
            return;
        }

        // Ambil User dan Kategori untuk random assignment
        $userIds = User::pluck('id')->toArray();
        $categoryIds = Category::pluck('id')->toArray();

        $titles = [
            'Sunset di Pantai', 'Gunung yang Indah', 'Kucing Lucu', 'Kota Cyberpunk',
            'Hutan Ajaib', 'Kopi Pagi', 'Langit Biru', 'Bunga Sakura',
            'Mobil Balap', 'Gedung Pencakar Langit', 'Makanan Enak', 'Laptop Gaming',
            'Konser Musik', 'Lukisan Abstrak', 'Rumah Minimalis', 'Jalanan Sepi'
        ];

        $this->command->info('Mulai menduplikasi foto...');

        for ($i = 0; $i < 20; $i++) {
            // Pilih gambar random
            $sourcePath = $images[array_rand($images)];
            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            
            // Buat nama file baru unik
            $newFileName = 'duplicate_' . Str::random(10) . '.' . $extension;
            $newPath = 'foto/' . $newFileName;

            // Copy file fisik
            Storage::disk('public')->copy($sourcePath, $newPath);

            // Buat record database
            Foto::create([
                'judul_foto' => $titles[array_rand($titles)] . ' ' . rand(1, 100),
                'deskripsi_foto' => 'Deskripsi otomatis untuk foto percobaan #' . ($i + 1),
                'lokasi_file' => $newFileName,
                'user_id' => $userIds[array_rand($userIds)],
                'category_id' => !empty($categoryIds) ? $categoryIds[array_rand($categoryIds)] : null,
                'status' => 'approved',
                'tanggal_unggah' => now(), // Setup tanggal_unggah
                'created_at' => now()->subDays(rand(0, 30)), // Tanggal acak bulan ini
            ]);
        }

        $this->command->info('Berhasil membuat 20 foto duplikat!');
    }
}
