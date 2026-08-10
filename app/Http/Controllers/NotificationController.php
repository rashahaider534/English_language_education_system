<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationservice
    ) {}

    public function getNotifications()
    {
        $notifications = $this->notificationservice->getNotifications(auth()->user());
        return NotificationResource::collection($notifications);
    }

    public function getUnreadCount()
    {
        $count = $this->notificationservice->getUnreadCount(auth()->user());
        return response()->json(['number of unread notification' => $count]);
    }

    public function markAsRead(AppNotification $notification)
    {
        $notification = $this->notificationservice->markAsRead(auth()->user(), $notification);
        return new NotificationResource($notification);
    }

    public function markAllAsRead()
    {
        $notifications = $this->notificationservice->markAllAsRead(auth()->user());
        return  NotificationResource::collection($notifications);
    }

    public function deleteNotification(AppNotification $notification)
    {
        return response()->json(
            $this->notificationservice->deleteNotification(auth()->user(), $notification)
        );
    }
    
}
