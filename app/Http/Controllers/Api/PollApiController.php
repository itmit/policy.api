<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Poll;
use App\PollQuestions;
use App\PollQuestionAnswers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PollApiController extends ApiBaseController
{
    /**
     * Выводит список опросов
     *
     * @return \Illuminate\Http\Response
     */
    public function getPollList()
    {
        $polls = Poll::all()->toArray();
        return $this->sendResponse($polls, 'Список опросов');
    }

    /**
     * Выводит список вопросов данного опроса
     *
     * @return \Illuminate\Http\Response
     */
    public function getPollQuestionList(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'poll_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $poll = Poll::where('uuid', '=', $request->poll_uuid)->first();

        if($poll == null)
        {
            return $this->sendError(0, 'Такого опроса не существует');
        }

        $questions = PollQuestions::where('poll_id', '=', $poll->id)->get();

        $response = [];

        foreach($questions as $question)
        {
            $response[] = [
                'question_uuid' => $question->uuid,
                'question' => $question->question,
            ];
            $response_answers = [];
            $question_answers = PollQuestionAnswers::where('question_id', '=', $question->id)->get();
            foreach($question_answers as $question_answer)
            {
                $response_answers ['answer'] = [
                    'answer_uuid' => $question_answer->uuid,
                    'answers' => $question_answer->answer
                ];
            }
            $response[] = [
                'answer' => $response_answers
            ];
            
        }

        return $this->sendResponse($response, 'Список вопросов');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
