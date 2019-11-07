<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'polls';

    public function category()
    {
        return $this->belongsTo(PollCategories::class, 'category')->get()->first();
    }

    // public function client() 
    // {
    //     return $this->belongsTo(Client::class, 'client')->get()->first();        
    // }
}
