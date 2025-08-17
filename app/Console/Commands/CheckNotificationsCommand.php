<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeploymentNotification;
use App\Models\DeployedItem;
use App\Models\User;

class CheckNotificationsCommand extends Command
{
    protected $signature = 'check:notifications {--user-id=}';
    protected $description = 'Check deployment notifications';

    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $this->info("Notifications for user ID {$userId}:");
            $notifications = DeploymentNotification::where('user_id', $userId)->get();
        } else {
            $this->info("All deployment notifications:");
            $notifications = DeploymentNotification::all();
        }
        
        if ($notifications->count() == 0) {
            $this->info("No notifications found.");
            return 0;
        }
        
        $this->info("ID | User | Type | Message | Read");
        $this->info("---|------|------|---------|-----");
        
        foreach ($notifications as $notification) {
            $user = User::find($notification->user_id);
            $userName = $user ? $user->name : 'Unknown';
            $message = substr($notification->message, 0, 30) . '...';
            $read = $notification->is_read ? 'Yes' : 'No';
            
            $this->line("{$notification->id} | {$userName} | {$notification->type} | {$message} | {$read}");
        }
        
        return 0;
    }
}
