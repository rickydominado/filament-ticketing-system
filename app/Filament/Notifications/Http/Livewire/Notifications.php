<?php

namespace App\Filament\Notifications\Http\Livewire;

use App\Models\Inquiry;
use Filament\Notifications\Http\Livewire\Notifications as BaseComponent;

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

        $inquiry = Inquiry::find($notification['viewData']['inquiry_id']);
        $inquiry->update(['has_notification' => true]);

        $this->dispatchDatabaseNotificationBrowserEvent();
    }

    public function dispatchDatabaseNotificationBrowserEvent()
    {
        return $this->dispatchBrowserEvent('receive-notification', ['notification' => $this->getUnreadDatabaseNotificationsCount()]);
    }
}
