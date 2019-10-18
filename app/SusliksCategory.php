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

    // public function client()
    // {
    //     // dd($this->belongsTo(Client::class, 'client')->get()->first());
    //     return $this->belongsTo(Client::class, 'client')->get()->first();
    // }
}
