<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LetterActivityEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->userId = (int) $notification->user_id; // ensure integer for channel
    }

    // Broadcast on a private channel per user
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    // Event name for JS listener
    public function broadcastAs()
    {
        return 'NotificationCreated';
    }

    // Data sent to frontend
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'message' => $this->notification->message ?? 'New notification',
            'read_at' => $this->notification->read_at,
            'created_at' => optional($this->notification->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
