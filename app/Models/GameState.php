<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameState extends Model
{
    protected $table = 'game_state';

    protected $fillable = ['lobby_id', 'current_card_id', 'round_number'];

    public $timestamps = false;

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }

    public function currentCard()
    {
        return $this->belongsTo(Card::class, 'current_card_id');
    }
}
