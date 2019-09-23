<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'favorites';
}
