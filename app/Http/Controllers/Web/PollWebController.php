<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Poll;
use App\PollCategories;
use App\PollQuestions;
use App\PollQuestionAnswers;
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
            // return $questions['question_name'];
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
    public function destroyCategory(Request $request)
    {
        PollCategories::destroy($request->input('id'));

        return response()->json(['Категория удалена']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyPoll($ids)
    {
        // Suslik::destroy($request->input('ids'));

        // return response()->json(['Places destroyed']);
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
                'required|min:3|max:191',
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
