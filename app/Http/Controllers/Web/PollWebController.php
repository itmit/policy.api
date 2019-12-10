<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Poll;
use App\PollCategories;
use App\PollQuestions;
use App\PollQuestionAnswers;
use App\PollQuestionAnswerUsers;
use App\UserToPoll;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PollWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('polls.polls', [
        'polls' => Poll::select('*')
            ->orderBy('created_at', 'desc')->get()
        ]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('polls.pollCreate', [
            'categories' => PollCategories::select('*')
            ->orderBy('created_at', 'desc')->get()
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $poll = Poll::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->all_data["name"],
            'description' => $request->all_data["description"],
            'category' => $request->all_data["category"],
            'link' => $request->all_data["link"],
            'start_at' => $request->all_data["start_at"],
            'end_at' => $request->all_data["end_at"],
        ]);

        // foreach($request->all_data["questions"] as $key => $value)
        // {
        //     PollQuestions::create([
        //         'uuid' => (string) Str::uuid(),
        //         'poll_id' => $request->all_data["name"],
        //         'question' => $request->all_data["description"],
        //         'multiple' => $request->all_data["category"],
        //     ]);
        // };

        // dd($request->all_data["questions"]);

        foreach($request->all_data["questions"] as $questions)
        {
            return $questions['answer_count'];
            
            if($questions['multiple'] == 'true') $questions['multiple'] = 1;
            if($questions['multiple'] == 'false') $questions['multiple'] = 0;
            $pollQuestion = PollQuestions::create([
                'uuid' => (string) Str::uuid(),
                'poll_id' => $poll->id,
                'question' => $questions['question_name'],
                'multiple' => $questions['multiple'],
            ]);
            foreach ($questions['answers'] as $key => $value) {
                    $pollQuestionAnswer = PollQuestionAnswers::create([
                        'uuid' => (string) Str::uuid(),
                        'question_id' => $pollQuestion->id,
                        'answer' => $value,
                        'type' => 0,
                    ]);
            }
            if($questions['other'] == 'true'){
                $pollQuestionAnswer = PollQuestionAnswers::create([
                    'uuid' => (string) Str::uuid(),
                    'question_id' => $pollQuestion->id,
                    'answer' => 'Другой',
                    'type' => 1,
                ]);
            }
            
            // foreach($questions as $key => $value)
            // {
            //     // $result .= ' key: ' . $key;
            //     PollQuestions::create([
            //         'uuid' => (string) Str::uuid(),
            //         'poll_id' => $poll->id,
            //         'question' => $request->all_data["description"],
            //         'multiple' => $request->all_data["category"],
            //     ]);
            //     if($key == 'answers')
            //     {
            //         foreach($key as $key2 => $value2)
            //         {
                        
            //         };
            //     }
            // };
        };


        // return $result;
        // dd($request->all_data["questions"]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $questions = PollQuestions::where('poll_id', '=', $id)->get();

        $response = [];
        $userAnswers = [];

        foreach($questions as $question)
        {
            $response_answers = [];
            $question_answers = PollQuestionAnswers::where('question_id', '=', $question->id)->get();
            foreach($question_answers as $question_answer)
            {
                $response_answers [] = [
                    'answer_id' => $question_answer->id,
                    'answer_uuid' => $question_answer->uuid,
                    'answer' => $question_answer->answer,
                    'type' => $question_answer->type,
                    'answers_count' => $question_answer->answers_count
                ];
                // $data = [];
                // $userAnswers[] = PollQuestionAnswerUsers::where('answer_id', '=', $question_answer->id)
            }
            $response[] = [
                'question_uuid' => $question->uuid,
                'question' => $question->question,
                'multiple' => $question->multiple,
                'answers' => $response_answers
            ];
        }

        $data = UserToPoll::where('poll_id', '=', $id)->get();

        // dd($response);

        return view('polls.pollDetail', [
            'poll' => Poll::where('id', '=', $id)->first(),
            'questions' => PollQuestions::where('poll_id', '=', $id)->get(),
            'response' => $response,
            'data' => $data
        ]); 
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
    public function destroyCategory(Request $request)
    {
        $isCategoryDeleted = PollCategories::where('id', $request->input('id'))->first(['name', 'id']);
        
        if($isCategoryDeleted['name'] == 'deleted')
        {
            return response()->json(['Произошла ошибка']);
        }

        $deleted = PollCategories::withTrashed()->where('name', '=', 'deleted')->first();

        Poll::where('category', $request->input('id'))->update([
            'category' => $deleted['id']
        ]);

        PollCategories::destroy($request->input('id'));

        return response()->json(['Категория удалена']);
    }

    public function showDeleted()
    {
        return response()->json([PollCategories::onlyTrashed()->where('name', '<>', 'deleted')->get()->toArray()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Poll::destroy($request->input('ids'));

        return response()->json(['Polls destroyed']);
    }

    public function createCategory()
    {
        return view('polls.createPollCategory', [
            'categories' => PollCategories::select('*')
            ->orderBy('created_at', 'desc')->get()
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                'max:191',
                Rule::notIn(['deleted']),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.createPollCategory')
                ->withErrors($validator)
                ->withInput();
        }

        PollCategories::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
        ]);

        return redirect()->route('auth.polls.index');
    }
}
