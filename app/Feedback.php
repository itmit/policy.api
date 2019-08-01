<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
     /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'message',
    ];

    /**
     * @var string
     */
    protected $table = 'feedback';
}
