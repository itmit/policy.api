<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SusliksCategory extends Model
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
    protected $table = 'susliks_categories';
}
