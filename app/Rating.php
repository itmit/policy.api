<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
     /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'like',
        'dislike',
        'neutral',
    ];

    /**
     * @var string
     */
    protected $table = 'user_rating';
}
