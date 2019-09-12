<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'polls';

    // public function shipmentGoods()
    // {
    //     return $this->hasMany(ShipmentGoods::class);
    // }

    // public function client() 
    // {
    //     return $this->belongsTo(Client::class, 'client')->get()->first();        
    // }
}
