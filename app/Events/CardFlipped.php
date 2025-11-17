<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardFlipped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $flipped;

    public function __construct($lobbyCode, $flipped)
    {
        $this->lobbyCode = $lobbyCode;
        $this->flipped = $flipped;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('lobby.' . $this->lobbyCode),
        ];
    }

    public function broadcastAs()
    {
        return 'CardFlipped';
    }

    public function broadcastWith()
    {
        return [
            'flipped' => $this->flipped,
        ];
    }
}
