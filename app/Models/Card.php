<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['id','name','image_url','description'];
    public $timestamps = false;
}
