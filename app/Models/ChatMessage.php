<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['lobby_id', 'sender', 'message'];

    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }
}