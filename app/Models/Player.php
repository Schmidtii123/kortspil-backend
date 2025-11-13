<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    // Disable created_at/updated_at since the table doesn't have them
    public $timestamps = false;

    protected $fillable = ['alias', 'lobby_id', 'is_dm'];

    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }
}
