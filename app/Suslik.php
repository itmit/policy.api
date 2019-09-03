<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suslik extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'place_of_work', 'position', 'category', 'photo', 'like', 'dislike', 'neutral'
    ];

    /**
     * @var string
     */
    protected $table = 'susliks';
}
