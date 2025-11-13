<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lobbyCode;

    /**
     * Create a new event instance.
     */
    public function __construct($lobbyCode)
    {
        $this->lobbyCode = $lobbyCode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return [
            'lobby_code' => $this->lobbyCode,
        ];
    }
}
