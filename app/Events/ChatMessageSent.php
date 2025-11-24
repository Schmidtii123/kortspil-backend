<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $lobbyCode,
        public string $sender,
        public string $message,
        public string $createdAt
    ) {}

    public function broadcastOn()
    {
        return new Channel('lobby.' . $this->lobbyCode);
    }

    public function broadcastAs()
    {
        return 'ChatMessageSent';
    }

    public function broadcastWith()
    {
        return [
            'sender'     => $this->sender,
            'message'    => $this->message,
            'created_at' => $this->createdAt,
        ];
    }
}