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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role_id bertipe integer
            // Kita beri nilai default '2' (User Biasa)
            // '1' nantinya akan kita gunakan untuk Admin
            // Diletakkan setelah kolom 'avatar' agar struktur tabel rapi
            $table->integer('role_id')->default(2)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('role_id');
        });
    }
};