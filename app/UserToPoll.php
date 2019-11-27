<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToPoll extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'user_to_polls';
}
