<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seeding data directly in migration for efficiency as requested
            $categories = ['Wallpaper', 'Anime', 'Nature', 'Meme', 'Gaming', 'Art', 'Photography'];
            foreach ($categories as $cat) {
                \Illuminate\Support\Facades\DB::table('categories')->insertOrIgnore([
                    'name' => $cat,
                    'slug' => \Illuminate\Support\Str::slug($cat),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
