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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->first();
    }
}
