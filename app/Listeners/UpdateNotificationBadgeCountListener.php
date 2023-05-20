<?php

namespace App\Listeners;

use App\Events\UpdateNotificationBadgeCountEvent;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateNotificationBadgeCountListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UpdateNotificationBadgeCountEvent $event): void
    {
        event(new DatabaseNotificationsSent($event->user));
    }
}
