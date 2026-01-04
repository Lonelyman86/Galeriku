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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->default(2)->constrained('roles'); // 2 = User Biasa
                $table->string('username');
                $table->string('password');
                $table->string('email')->unique();
                $table->string('fullname');
                $table->longText('avatar')->nullable();
                $table->text('bio')->nullable();
                // $table->text('address'); // Removed as per request
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
