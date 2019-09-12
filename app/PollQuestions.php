<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollQuestions extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'poll_questions';
}
