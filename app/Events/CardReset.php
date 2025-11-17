<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardReset implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lobbyCode;

    public function __construct($lobbyCode)
    {
        $this->lobbyCode = $lobbyCode;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('lobby.' . $this->lobbyCode),
        ];
    }

    public function broadcastAs()
    {
        return 'CardReset';
    }
}
