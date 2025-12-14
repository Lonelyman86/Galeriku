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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Pelapor
            $table->foreignId('foto_id')->constrained('foto')->cascadeOnDelete(); // Foto yang dilaporkan
            $table->text('reason'); // Alasan pelaporan
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending'); // Status laporan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
