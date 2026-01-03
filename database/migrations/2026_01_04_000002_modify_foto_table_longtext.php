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
        Schema::table('foto', function (Blueprint $table) {
            // Change lokasi_file column to LONGTEXT to support Base64 strings
            $table->longText('lokasi_file')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->string('lokasi_file')->change();
        });
    }
};
