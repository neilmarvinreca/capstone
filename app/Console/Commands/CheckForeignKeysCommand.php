<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckForeignKeysCommand extends Command
{
    protected $signature = 'check:foreign-keys';
    protected $description = 'Check foreign keys in the database';

    public function handle()
    {
        $this->info("Checking foreign keys...");
        
        // Check supplies table
        $this->info("\nSupplies table foreign keys:");
        $suppliesCreate = DB::select('SHOW CREATE TABLE supplies')[0]->{'Create Table'};
        if (strpos($suppliesCreate, 'FOREIGN KEY') !== false) {
            $this->line("Supplies table has foreign keys");
        } else {
            $this->line("Supplies table has no foreign keys");
        }
        
        // Check users table
        $this->info("\nUsers table foreign keys:");
        $usersCreate = DB::select('SHOW CREATE TABLE users')[0]->{'Create Table'};
        if (strpos($usersCreate, 'FOREIGN KEY') !== false) {
            $this->line("Users table has foreign keys");
        } else {
            $this->line("Users table has no foreign keys");
        }
        
        // Check deployed_items table
        $this->info("\nDeployed_items table foreign keys:");
        $deployedItemsCreate = DB::select('SHOW CREATE TABLE deployed_items')[0]->{'Create Table'};
        if (strpos($deployedItemsCreate, 'FOREIGN KEY') !== false) {
            $this->line("Deployed_items table has foreign keys");
        } else {
            $this->line("Deployed_items table has no foreign keys");
        }
        
        return 0;
    }
}
