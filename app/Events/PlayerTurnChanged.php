<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerTurnChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $playerName;

    /**
     * Create a new event instance.
     */
    public function __construct($lobbyCode, $playerName)
    {
        $this->lobbyCode = $lobbyCode;
        $this->playerName = $playerName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('lobby.' . $this->lobbyCode),
        ];
    }

    public function broadcastWith()
    {
        return [
            'player_name' => $this->playerName,
        ];
    }
}
