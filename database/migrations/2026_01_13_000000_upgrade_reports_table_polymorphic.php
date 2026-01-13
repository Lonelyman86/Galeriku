<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop foreign key if it exists (assuming standard naming)
            // Need to be careful with exact name. Usually reports_foto_id_foreign
            // Using check to be safe or just try catch block in raw sql?
            // Better to just modify columns.

            // Drop the old column
            $table->dropForeign(['foto_id']);
            $table->dropColumn('foto_id');

            // Add polymorphic columns
            $table->morphs('reportable'); // Adds reportable_id, reportable_type
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropMorphs('reportable');
            $table->unsignedBigInteger('foto_id');
            $table->foreign('foto_id')->references('id')->on('foto')->onDelete('cascade');
        });
    }
};
