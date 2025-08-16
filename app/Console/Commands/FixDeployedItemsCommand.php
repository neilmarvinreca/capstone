<?php

namespace App\Console\Commands;

use App\Models\DeployedItem;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixDeployedItemsCommand extends Command
{
    protected $signature = 'deployed-items:fix-ids';
    protected $description = 'Fix deployed items with invalid IDs';

    public function handle()
    {
        $items = DeployedItem::where('deployedID', 0)->get();
        
        if ($items->isEmpty()) {
            $this->info('No items with deployedID = 0 found.');
            return 0;
        }
        
        $this->info("Found {$items->count()} items with deployedID = 0. Updating...");
        
        $bar = $this->output->createProgressBar($items->count());
        $bar->start();
        
        foreach ($items as $item) {
            $item->update([
                'deployedID' => 'DP-' . strtoupper(Str::random(8))
            ]);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('All items have been updated successfully!');
        
        return 0;
    }
}
