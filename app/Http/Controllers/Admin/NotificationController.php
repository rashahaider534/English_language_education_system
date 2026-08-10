<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\AppNotification;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    public function index()
    {
        $notifications = $this->notificationService->getNotifications(auth()->user());
        $unreadCount = $this->notificationService->getUnreadCount(auth()->user());

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(AppNotification $notification)
    {
        $this->notificationService->markAsRead(auth()->user(), $notification);
        return redirect()
        ->route('admin.notifications.index')
        ->with('success', 'تم تحديد الإشعار كمقروء.');
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());
        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    public function deleteNotification(AppNotification $notification)
    {
        $this->notificationService->deleteNotification(auth()->user(), $notification);
        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'تم حذف الإشعار.');
    }
}
