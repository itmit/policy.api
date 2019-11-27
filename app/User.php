<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rating()
    {
        return $this->hasOne(Rating::class, 'user_id')->get()->first();
    }

    public function userAnswer()
    {
        return $this->hasOne(PollQuestionAnswerUsers::class, 'user_id')->get();
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'id')->first();
    }
}
