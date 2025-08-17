<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListTablesCommand extends Command
{
    protected $signature = 'list:tables';
    protected $description = 'List all database tables';

    public function handle()
    {
        $this->info("All database tables:");
        
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $this->line($tableName);
        }
        
        return 0;
    }
}
