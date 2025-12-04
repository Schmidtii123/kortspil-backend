<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerKicked implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $lobbyCode,
        public string $kickedAlias
    ) {}

    public function broadcastOn()
    {
        return new Channel('lobby.' . $this->lobbyCode);
    }

    public function broadcastWith()
    {
        return ['kicked_alias' => $this->kickedAlias];
    }

    public function broadcastAs()
    {
        return 'PlayerKicked';
    }
}