<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
     /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'like',
        'dislike',
        'neutral',
    ];

    /**
     * @var string
     */
    protected $table = 'user_rating';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->get()->first();
    }
}
