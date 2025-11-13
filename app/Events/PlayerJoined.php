<?php

namespace App\Events;

use App\Models\Lobby;
use App\Models\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerJoined implements ShouldBroadcast
{
    public $player;
    public $lobbyCode;

    public function __construct(Lobby $lobby, Player $player)
    {
        $this->player = $player;
        $this->lobbyCode = $lobby->lobby_code;
    }

    public function broadcastOn()
    {
        return new Channel('lobby.' . $this->lobbyCode);
    }
}
