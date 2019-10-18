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
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'susliks';

    public function category()
    {
        // dd($this->belongsTo(Client::class, 'client')->get()->first());
        return $this->belongsTo(SusliksCategory::class, 'category')->get()->first();
    }
}
