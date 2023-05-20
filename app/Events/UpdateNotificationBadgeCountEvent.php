<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class UpdateNotificationBadgeCountEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model | Authenticatable $user;
    public int $notifications;

    /**
     * Create a new event instance.
     */
    public function __construct(Model|Authenticatable $user, int $notifications)
    {
        $this->user = $user;
        $this->notifications = $notifications;
    }

    public function broadcastOn(): string
    {
        if (method_exists($this->user, 'receivesBroadcastNotificationsOn')) {
            return new PrivateChannel($this->user->receivesBroadcastNotificationsOn());
        }

        $userClass = str_replace('\\', '.', $this->user::class);

        return new PrivateChannel("{$userClass}.{$this->user->getKey()}");
    }

    public function broadcastAs(): string
    {
        return 'notification-badge-count.sent';
    }
}
