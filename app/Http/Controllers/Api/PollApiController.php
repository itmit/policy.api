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

        $poll = Poll::where('uuid', '=', $request->poll_uuid)->get();

        if($poll == null)
        {
            return $this->sendError(0, 'Такого опроса не существует');
        }

        $questions = PollQuestions::where('poll_id', '=', $poll->id)->get();

        return $this->sendResponse([$questions], 'Список вопросов');
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
