<?php

namespace App\Http\Controllers;

use App\Services\DeploymentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $success = DeploymentNotificationService::markAsRead($notificationId, Auth::id());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Notification marked as read' : 'Notification not found'
            ]);
        }
        
        return back()->with($success ? 'success' : 'error', 
            $success ? 'Notification marked as read' : 'Notification not found');
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllAsRead(Request $request)
    {
        $count = DeploymentNotificationService::markAllAsRead(Auth::id());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Marked {$count} notifications as read"
            ]);
        }
        
        return back()->with('success', "Marked {$count} notifications as read");
    }

    /**
     * Get notifications for the current user.
     */
    public function getNotifications(Request $request)
    {
        $notifications = DeploymentNotificationService::getUserNotifications(
            Auth::id(), 
            $request->get('limit', 10)
        );
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        }
        
        return view('notifications.index', compact('notifications'));
    }
}
