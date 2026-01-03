<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = ['Wallpaper', 'Anime', 'Nature', 'Meme', 'Gaming', 'Art', 'Photography'];

        foreach ($categories as $cat) {
            // Check existence to avoid duplicate entry errors
            if (DB::table('categories')->where('name', $cat)->doesntExist()) {
                DB::table('categories')->insert([
                    'name' => $cat,
                    'slug' => Str::slug($cat),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Delete the seeded categories
        // DB::table('categories')->whereIn('name', ['Wallpaper', 'Anime', 'Nature', 'Meme', 'Gaming', 'Art', 'Photography'])->delete();
    }
};
