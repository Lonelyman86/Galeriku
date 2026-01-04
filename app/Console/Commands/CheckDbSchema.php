<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckDbSchema extends Command
{
    protected $signature = 'db:check-schema';
    protected $description = 'Check users table schema';

    public function handle()
    {
        $columns = Schema::getColumnListing('users');
        $this->info('Columns: ' . implode(', ', $columns));

        // Get column details specifically for MySQL/MariaDB (Aiven)
        $result = DB::select("SHOW COLUMNS FROM users WHERE Field = 'avatar'");

        if (!empty($result)) {
            $type = $result[0]->Type;
            $this->info("Avatar Column Type: " . $type);
        } else {
            $this->error("Avatar column not found!");
        }
    }
}
