<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\DeployedItem;
use App\Models\DeploymentNotification;
use App\Services\DeploymentNotificationService;

class TestDeploymentNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test department if it doesn't exist
        $department = Department::firstOrCreate(
            ['officename' => 'Test Department'],
            [
                'locationcode' => 'TEST-LOC-001',
                'description' => 'Test department for notifications'
            ]
        );

        // Create a test department user if it doesn't exist
        $departmentUser = User::firstOrCreate(
            ['email' => 'deptuser@test.com'],
            [
                'name' => 'Test Department User',
                'password' => bcrypt('password'),
                'role' => 'Department User',
                'department_id' => $department->id
            ]
        );

        // Create a test inventory manager if it doesn't exist
        $inventoryManager = User::firstOrCreate(
            ['email' => 'inventory@test.com'],
            [
                'name' => 'Test Inventory Manager',
                'password' => bcrypt('password'),
                'role' => 'Inventory Manager'
            ]
        );

        // Create a test deployed item if it doesn't exist
        $deployedItem = DeployedItem::firstOrCreate(
            ['deployedID' => 'TEST-DEP-001'],
            [
                'itemName' => 'Test Laptop',
                'itemDescription' => 'Test laptop for notifications',
                'dateAcquired' => now(),
                'dateDeployed' => now(),
                'cost' => 50000.00,
                'itemCategory' => 'Electronics',
                'qr_code' => 'TEST-QR-001',
                'departmentID' => $department->departmentID,
                'status' => 'active',
                'deployed_by' => $inventoryManager->id,
                'quantity' => 1
            ]
        );

        // Create a test notification
        DeploymentNotification::firstOrCreate(
            [
                'user_id' => $departmentUser->id,
                'deployed_item_id' => $deployedItem->deployedID
            ],
            [
                'message' => 'New item \'Test Laptop\' has been deployed to your department',
                'type' => 'deployment',
                'is_read' => false,
                'data' => [
                    'item_name' => 'Test Laptop',
                    'item_description' => 'Test laptop for notifications',
                    'quantity' => 1,
                    'deployed_by' => 'Test Inventory Manager',
                    'deployment_date' => now()->format('M d, Y'),
                    'department' => 'Test Department'
                ]
            ]
        );

        $this->command->info('Test deployment notification data seeded successfully!');
        $this->command->info('Test Department User: deptuser@test.com / password');
        $this->command->info('Test Inventory Manager: inventory@test.com / password');
    }
}
