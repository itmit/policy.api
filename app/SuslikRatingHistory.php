<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuslikRatingHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'suslik_rating_histories';
}
