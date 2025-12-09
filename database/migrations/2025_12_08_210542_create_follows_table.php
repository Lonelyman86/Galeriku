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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            
            // 1. Orang yang melakukan follow (Pengikut)
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            
            // 2. Orang yang di-follow (Target)
            $table->foreignId('followed_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();

            // 3. Mencegah user follow orang yang sama berkali-kali
            $table->unique(['follower_id', 'followed_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};