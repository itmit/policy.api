<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Poll;
use App\PollQuestions;
use App\PollQuestionAnswers;
use App\PollQuestionAnswerUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
     * Выводит список вопросов и ответов данного опроса
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
            $response_answers = [];
            $question_answers = PollQuestionAnswers::where('question_id', '=', $question->id)->get();
            foreach($question_answers as $question_answer)
            {
                $response_answers [] = [
                    'answer_uuid' => $question_answer->uuid,
                    'answer' => $question_answer->answer,
                    'type' => $question_answer->type
                ];
            }
            $response[] = [
                'question_uuid' => $question->uuid,
                'question' => $question->question,
                'multiple' => $question->multiple,
                'answers' => $response_answers
            ];
            // $response[] = [
                
            // ];
            
        }

        return $this->sendResponse($response, 'Список вопросов');
    }

    /**
     * Прохождение опроса пользователем
     *
     * @return \Illuminate\Http\Response
     */
    public function passPoll(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'user_answer' => 'required|array',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        print_r($request->user_answer);

        foreach ($request->user_answer as $question_uuids => $answer_uuids) {

            echo $question_uuids . '</br>';

            // foreach ($question_uuids as $answer_uuid => $text) {
            //     $answer_id = PollQuestionAnswers::where('uuid', '=', $answer_uuid)->first(['id', 'type']);
            //     if($answer_id->type == 0) // обычный ответ, не другой
            //     {
            //         PollQuestionAnswerUsers::create([
            //             'uuid' => (string) Str::uuid(),
            //             'answer_id' => $answer_id->id,
            //             'user ' => auth('api')->user()->id,
            //         ]);
            //     }
            //     else
            //     {
            //         PollQuestionAnswerUsers::create([
            //             'uuid' => (string) Str::uuid(),
            //             'answer_id' => $answer_id->id,
            //             'user ' => auth('api')->user()->id,
            //             'other ' => $text,
            //         ]);
            //     }
            // }

        }

        // $response = PollQuestionAnswerUsers::all();
        // print_r($response);
    }

    // PollQuestionAnswerUsers::create([
    //     'uuid' => (string) Str::uuid(),
    //     'answer_id' => ,
    //     'user ' => ,
    // ]);

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
