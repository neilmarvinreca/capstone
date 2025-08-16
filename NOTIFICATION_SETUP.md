# Deployment Notification System Setup

This document explains how to set up and use the deployment notification system for department users when inventory managers deploy items to them.

## Overview

The notification system automatically creates notifications for department users whenever:
- An inventory manager deploys items to their department
- Items are deployed through bulk deployment
- Items are deployed from the supply inventory

## Database Setup

### 1. Run Migrations

First, run the new migrations to create the necessary database tables:

```bash
php artisan migrate
```

This will create:
- `department_id` column in the `users` table
- `deployment_notifications` table for storing notifications

### 2. Update Existing Users

If you have existing users, you may need to assign them to departments. You can do this through the admin panel or by updating the database directly.

## How It Works

### 1. Automatic Notification Creation

When an inventory manager deploys items, the system automatically:
- Creates a `DeployedItem` record
- Identifies all department users in the target department
- Creates `DeploymentNotification` records for each department user

### 2. Notification Display

Department users will see:
- A green notification bell icon in the top bar when they have unread notifications
- A dropdown showing recent deployment notifications
- A dedicated notifications page accessible from the sidebar

### 3. Notification Management

Users can:
- Click on notifications to mark them as read
- View all their notifications on a dedicated page
- Mark all notifications as read at once

## Testing the System

### 1. Run the Test Seeder

To test the notification system with sample data:

```bash
php artisan db:seed --class=TestDeploymentNotificationSeeder
```

This creates:
- A test department
- A test department user (deptuser@test.com / password)
- A test inventory manager (inventory@test.com / password)
- A test deployed item
- A test notification

### 2. Test Scenarios

1. **Login as Department User**: Use `deptuser@test.com` / `password`
   - You should see a green notification bell in the top bar
   - Click it to see the deployment notification
   - Check the sidebar for a "Notifications" link

2. **Login as Inventory Manager**: Use `inventory@test.com` / `password`
   - Deploy items to the test department
   - Check that notifications are created for department users

## File Structure

```
app/
├── Models/
│   ├── DeploymentNotification.php          # Notification model
│   ├── DeployedItem.php                   # Updated with notification relationship
│   └── User.php                           # Updated with department relationship
├── Services/
│   └── DeploymentNotificationService.php   # Notification business logic
├── Http/Controllers/
│   ├── DeployedItemController.php         # Updated to create notifications
│   └── NotificationController.php         # Handles notification actions
└── resources/views/
    ├── layouts/
    │   ├── top-bar.blade.php              # Updated with notification display
    │   └── sidebar.blade.php              # Added notifications link
    └── notifications/
        └── index.blade.php                 # Notifications page
```

## Routes

The following routes are added for notification management:

```php
// Notifications
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'getNotifications'])->name('index');
    Route::patch('{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::patch('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});
```

## Customization

### 1. Notification Content

You can customize the notification message and data in `DeploymentNotificationService::createDeploymentNotifications()`.

### 2. Notification Types

The system supports different notification types. You can extend it by:
- Adding new notification types
- Creating different notification templates
- Implementing email notifications

### 3. Styling

The notification UI uses Tailwind CSS classes. You can customize the appearance by modifying the CSS classes in the Blade templates.

## Troubleshooting

### 1. Notifications Not Appearing

Check:
- User has `department_id` set
- User role is "Department User"
- DeployedItem has correct `departmentID`
- Database migrations have been run

### 2. Database Errors

Ensure:
- All migrations have been run successfully
- Foreign key constraints are properly set up
- Database user has necessary permissions

### 3. Performance Issues

For large deployments:
- Consider queuing notification creation
- Implement pagination for notifications
- Add database indexes if needed

## Future Enhancements

Potential improvements:
- Email notifications
- Push notifications
- Notification preferences
- Bulk notification actions
- Notification history
- Real-time updates using WebSockets
