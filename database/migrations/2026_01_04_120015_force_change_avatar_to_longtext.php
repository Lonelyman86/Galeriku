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
        // Use raw SQL to force the change, bypassing potential Doctrine DBAL issues
        DB::statement('ALTER TABLE users MODIFY avatar LONGTEXT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY avatar VARCHAR(255)');
    }
};
