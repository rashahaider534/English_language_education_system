<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class NotificationService
{
    public function getNotifications(User $user)
    {
         $unread = $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
        $read = $user->notifications()
            ->whereNotNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
         $notifications = $unread->concat($read);
         return $notifications;
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->where('read', false)
            ->count();
    }

    public function markAsRead(User $user, AppNotification $notification): AppNotification
    {
        if ($notification->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'notification' => 'You cannot mark this notification as read.',
            ]);
        }

        if (!$notification->read) {
            $notification->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }

        return $notification->fresh();
    }

    public function markAllAsRead(User $user)
    {
        $user->notifications()
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return $user->notifications()
            ->latest()
            ->get();
    }

    public function deleteNotification(User $user, AppNotification $notification)
    {
        if ($notification->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'notification' => 'You cannot delete this notification.',
            ]);
        }
        $notification->delete();
        return ['Notification deleted'];
    }

}
