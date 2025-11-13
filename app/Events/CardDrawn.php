<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardDrawn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lobbyCode;
    public $cardNumber;

    /**
     * Create a new event instance.
     */
    public function __construct($lobbyCode, $cardNumber)
    {
        $this->lobbyCode = $lobbyCode;
        $this->cardNumber = $cardNumber;
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
            'card_number' => $this->cardNumber,
        ];
    }
}
