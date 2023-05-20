<?php

namespace App\Filament\Notifications\Http\Livewire;

use App\Models\Inquiry;
use Filament\Notifications\Http\Livewire\Notifications as BaseComponent;
use Filament\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;

class Notifications extends BaseComponent
{
    // Used to check if Livewire messages should trigger notification animations.
    public bool $isFilamentNotificationsComponent = false;

    public function removeNotification(string $id): void
    {
        parent::removeNotification($id);

        $this->dispatchDatabaseNotificationBrowserEvent();
    }

    public function clearDatabaseNotifications(): void
    {
        parent::clearDatabaseNotifications();

        $this->dispatchDatabaseNotificationBrowserEvent();
    }

    public function markAllDatabaseNotificationsAsRead(): void
    {
        parent::markAllDatabaseNotificationsAsRead();

        $this->dispatchDatabaseNotificationBrowserEvent();
    }

    public function handleBroadcastNotification($notification): void
    {
        parent::handleBroadcastNotification($notification);

        $this->dispatchDatabaseNotificationBrowserEvent();
    }

    public function getNotificationFromDatabaseRecord(DatabaseNotification $notification): Notification
    {
        $inquiry = Inquiry::find($notification['data']['viewData']['inquiry_id']);
        $inquiry->update(['has_notification' => true]);

        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    public function dispatchDatabaseNotificationBrowserEvent()
    {
        return $this->dispatchBrowserEvent('receive-notification', ['notifications' => $this->getUnreadDatabaseNotificationsCount()]);
    }
}
