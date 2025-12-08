<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeft implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $lobbyCode,
        public string $playerAlias
    ) {}

    public function broadcastOn()
    {
        return new Channel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return ['player_alias' => $this->playerAlias];
    }

    public function broadcastAs()
    {
        return 'PlayerLeft';
    }
}