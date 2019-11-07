<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollCategories extends Model
{
    use SoftDeletes;
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
