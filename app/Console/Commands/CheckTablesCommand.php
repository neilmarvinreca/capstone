<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTablesCommand extends Command
{
    protected $signature = 'check:tables';
    protected $description = 'Check database tables';

    public function handle()
    {
        $this->info("Database tables:");
        
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $this->line($tableName);
        }
        
        return 0;
    }
}
