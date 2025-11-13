<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lobby extends Model
{
    protected $fillable = ['lobby_code', 'dm_id', 'is_active'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
