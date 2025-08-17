<?php

namespace App\Services;

use App\Models\DeployedItem;
use App\Models\DeploymentNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeploymentNotificationService
{
    /**
     * Create notifications for department users when items are deployed to their department.
     */
    public static function createDeploymentNotifications(DeployedItem $deployedItem)
    {
        try {
            // Debug information
            Log::info("Starting deployment notifications for item: {$deployedItem->itemName}");
            Log::info("Department ID: {$deployedItem->departmentID}");
            
            // Get all users in the department where the item was deployed
            $departmentUsers = User::where('department_id', $deployedItem->departmentID)
                ->where('role', User::ROLE_DEPARTMENT_USER)
                ->get();

            Log::info("Found {$departmentUsers->count()} department users");

            // Get the accountable person for the department
            $accountablePerson = null;
            if ($deployedItem->department) {
                Log::info("Department found: {$deployedItem->department->officename}");
                Log::info("Accountable person ID: {$deployedItem->department->accountableper}");
                
                if ($deployedItem->department->accountableper) {
                    $accountablePerson = User::find($deployedItem->department->accountableper);
                    if ($accountablePerson) {
                        Log::info("Accountable person found: {$accountablePerson->name} (ID: {$accountablePerson->id})");
                    } else {
                        Log::warning("Accountable person not found for ID: {$deployedItem->department->accountableper}");
                    }
                } else {
                    Log::warning("No accountable person assigned to department: {$deployedItem->department->officename}");
                }
            } else {
                Log::warning("Department not found for ID: {$deployedItem->departmentID}");
            }

            // Create notifications for all department users
            foreach ($departmentUsers as $user) {
                DeploymentNotification::create([
                    'user_id' => $user->id,
                    'deployed_item_id' => $deployedItem->deployedID,
                    'message' => "New item '{$deployedItem->itemName}' has been deployed to your department",
                    'type' => 'deployment',
                    'is_read' => false,
                    'data' => [
                        'item_name' => $deployedItem->itemName,
                        'item_description' => $deployedItem->itemDescription,
                        'quantity' => $deployedItem->quantity ?? 1,
                        'deployed_by' => $deployedItem->deployedBy?->name ?? 'System',
                        'deployment_date' => $deployedItem->dateDeployed?->format('M d, Y') ?? now()->format('M d, Y'),
                        'department' => $deployedItem->department?->officename ?? 'Unknown Department'
                    ]
                ]);
                Log::info("Created notification for department user: {$user->name}");
            }

            // Create special notification for the accountable person
            if ($accountablePerson) {
                DeploymentNotification::create([
                    'user_id' => $accountablePerson->id,
                    'deployed_item_id' => $deployedItem->deployedID,
                    'message' => "New item '{$deployedItem->itemName}' has been deployed to your department as the accountable person",
                    'type' => 'deployment_accountable',
                    'is_read' => false,
                    'data' => [
                        'item_name' => $deployedItem->itemName,
                        'item_description' => $deployedItem->itemDescription,
                        'quantity' => $deployedItem->quantity ?? 1,
                        'deployed_by' => $deployedItem->deployedBy?->name ?? 'System',
                        'deployment_date' => $deployedItem->dateDeployed?->format('M d, Y') ?? now()->format('M d, Y'),
                        'department' => $deployedItem->department?->officename ?? 'Unknown Department',
                        'is_accountable_person' => true,
                        'deployment_value' => $deployedItem->cost ?? 0
                    ]
                ]);
                Log::info("Created accountable person notification for: {$accountablePerson->name}");
            } else {
                Log::warning("No accountable person notification created - accountable person not found");
            }

            Log::info("Completed deployment notifications for {$deployedItem->itemName} to department {$deployedItem->departmentID}");

        } catch (\Exception $e) {
            Log::error("Failed to create deployment notifications: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Mark a notification as read.
     */
    public static function markAsRead($notificationId, $userId)
    {
        $notification = DeploymentNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsRead($userId)
    {
        return DeploymentNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread notification count for a user.
     */
    public static function getUnreadCount($userId)
    {
        return DeploymentNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get notifications for a user.
     */
    public static function getUserNotifications($userId, $limit = 10)
    {
        return DeploymentNotification::where('user_id', $userId)
            ->with(['deployedItem.department', 'deployedItem.deployedBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
