<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckColumnsCommand extends Command
{
    protected $signature = 'check:columns {table}';
    protected $description = 'Check column names in a table';

    public function handle()
    {
        $table = $this->argument('table');
        
        $this->info("Columns in {$table} table:");
        
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        foreach ($columns as $column) {
            $this->line("- {$column->Field} ({$column->Type})");
        }
        
        return 0;
    }
}
