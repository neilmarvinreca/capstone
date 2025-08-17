<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeployedItem;
use App\Services\DeploymentNotificationService;

class CreateNotificationsCommand extends Command
{
    protected $signature = 'create:notifications {--deployed-item-id=}';
    protected $description = 'Create notifications for deployed items';

    public function handle()
    {
        $deployedItemId = $this->option('deployed-item-id');
        
        if ($deployedItemId) {
            $deployedItem = DeployedItem::find($deployedItemId);
            if (!$deployedItem) {
                $this->error("Deployed item with ID {$deployedItemId} not found");
                return 1;
            }
        } else {
            // Get the most recent deployed item
            $deployedItem = DeployedItem::latest()->first();
            if (!$deployedItem) {
                $this->error("No deployed items found in the database");
                return 1;
            }
        }

        $this->info("Creating notifications for deployed item: {$deployedItem->itemName}");
        $this->info("Department ID: {$deployedItem->departmentID}");
        
        // Create notifications
        DeploymentNotificationService::createDeploymentNotifications($deployedItem);
        
        $this->info("Notifications created successfully!");
        return 0;
    }
}
