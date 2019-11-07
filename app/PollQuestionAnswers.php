<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollQuestionAnswers extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'poll_question_answers';
}
