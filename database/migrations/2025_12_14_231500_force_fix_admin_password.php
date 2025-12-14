<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita paksa update password lewat Database langsung (bukan lewat Model)
        // Ini menghindari konflik dengan fitur 'Casting' di Laravel
        
        $email = 'admin@galeriku.com';
        
        // Cek dulu apakah user ada
        $exists = DB::table('users')->where('email', $email)->exists();
        
        if ($exists) {
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'password' => Hash::make('password123') // Kita enkripsi manual di sini
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
