<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to force the column change without needing doctrine/dbal
        DB::statement('ALTER TABLE foto MODIFY lokasi_file LONGTEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to VARCHAR(255) - Warning: Data might be truncated
        DB::statement('ALTER TABLE foto MODIFY lokasi_file VARCHAR(255)');
    }
};
