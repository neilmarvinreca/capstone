<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeployedItem;
use App\Models\Department;
use App\Models\User;
use App\Services\DeploymentNotificationService;
use Illuminate\Support\Facades\Log;

class TestNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--deployed-item-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system for deployed items';

    /**
     * Execute the console command.
     */
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

        $this->info("Testing notifications for deployed item: {$deployedItem->itemName}");
        $this->info("Department ID: {$deployedItem->departmentID}");
        
        // Check if department exists
        $department = Department::find($deployedItem->departmentID);
        if (!$department) {
            $this->error("Department with ID {$deployedItem->departmentID} not found");
            return 1;
        }
        
        $this->info("Department: {$department->officename}");
        $this->info("Accountable person ID: {$department->accountableper}");
        
        // Check if accountable person exists
        if ($department->accountableper) {
            $accountablePerson = User::find($department->accountableper);
            if ($accountablePerson) {
                $this->info("Accountable person: {$accountablePerson->name} (ID: {$accountablePerson->id})");
                $this->info("Accountable person role: {$accountablePerson->role}");
            } else {
                $this->error("Accountable person with ID {$department->accountableper} not found");
            }
        } else {
            $this->error("No accountable person assigned to this department");
        }
        
        // Check department users
        $departmentUsers = User::where('department_id', $deployedItem->departmentID)
            ->where('role', User::ROLE_DEPARTMENT_USER)
            ->get();
            
        $this->info("Department users found: {$departmentUsers->count()}");
        foreach ($departmentUsers as $user) {
            $this->info("- {$user->name} (ID: {$user->id}, Role: {$user->role})");
        }
        
        // Check existing notifications
        $this->info("\nChecking existing notifications...");
        $notifications = \App\Models\DeploymentNotification::where('deployed_item_id', $deployedItem->deployedID)->get();
        $this->info("Existing notifications for this item: {$notifications->count()}");
        
        foreach ($notifications as $notification) {
            $user = User::find($notification->user_id);
            $this->info("- User: {$user->name} (ID: {$user->id}), Type: {$notification->type}, Read: " . ($notification->is_read ? 'Yes' : 'No'));
        }
        
        // Test creating notifications
        $this->info("\nTesting notification creation...");
        DeploymentNotificationService::createDeploymentNotifications($deployedItem);
        
        // Check notifications again
        $this->info("\nChecking notifications after creation...");
        $notifications = \App\Models\DeploymentNotification::where('deployed_item_id', $deployedItem->deployedID)->get();
        $this->info("Total notifications for this item: {$notifications->count()}");
        
        foreach ($notifications as $notification) {
            $user = User::find($notification->user_id);
            $this->info("- User: {$user->name} (ID: {$user->id}), Type: {$notification->type}, Read: " . ($notification->is_read ? 'Yes' : 'No'));
        }
        
        $this->info("\nTest completed. Check the logs for detailed information.");
        return 0;
    }
}
