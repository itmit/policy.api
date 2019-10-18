<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollCategories extends Model
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
    protected $table = 'poll_categories';
}
