<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['name', 'image_url', 'description'];

    public $timestamps = false;

    public function gameStates()
    {
        return $this->hasMany(GameState::class, 'current_card_id');
    }
}
