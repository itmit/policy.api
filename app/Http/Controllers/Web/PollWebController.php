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
use PDF;

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

        foreach($request->all_data["questions"] as $questions)
        {
            // return $questions['answer_count'][0];

            if($questions['multiple'] == 'true') $questions['multiple'] = 1;
            if($questions['multiple'] == 'false') $questions['multiple'] = 0;

            $pollQuestion = PollQuestions::create([
                'uuid' => (string) Str::uuid(),
                'poll_id' => $poll->id,
                'question' => $questions['question_name'],
                'multiple' => $questions['multiple'],
            ]);

            $i = 0;

            foreach ($questions['answers'] as $key => $value) {
                $pollQuestionAnswer = PollQuestionAnswers::create([
                    'uuid' => (string) Str::uuid(),
                    'question_id' => $pollQuestion->id,
                    'answer' => $value,
                    'answers_count' => $questions['answer_count'][$i],
                    'type' => 0,
                ]);
                $i++;
            }

            if($questions['other'] == 'true'){
                $pollQuestionAnswer = PollQuestionAnswers::create([
                    'uuid' => (string) Str::uuid(),
                    'question_id' => $pollQuestion->id,
                    'answer' => 'Другой',
                    'type' => 1,
                ]);
            }
        };

        if($request->all_data["notification"]) self::sendPush();
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
            'parent' => $request->subcategory,
        ]);

        return redirect()->route('auth.polls.index');
    }

    public function sendPush()
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array (
            'to' => '/topics/all',
            "notification" => [
                "body" => "Приглашаем пройти новый опрос!",
                "title" => "Простая статистика"
            ]
        );
        $fields = json_encode ( $fields );

        $headers = array (
                'Authorization: key=AAAAUnlyV4I:APA91bGiQmLus9UnVp1yBggANKWsZZRk0tFTgOYOV0ayiEOzHArSzTTWo5OF9IvNnaQBZbgF1LhnDYU2HaYhCQEkAWpGBBPPLYehnuqYe-tUf2z9fK5deWGSF2N3xYX14f1_m20KnF17',
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        curl_exec ( $ch );

        curl_close ( $ch );
    }

    public function downloadPDF(Request $request)
    {
        $id = $request->id;
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
            }
            $response[] = [
                'question_uuid' => $question->uuid,
                'question' => $question->question,
                'multiple' => $question->multiple,
                'answers' => $response_answers
            ];
        }

        $data = UserToPoll::where('poll_id', '=', $id)->get();

        $pdf = PDF::loadView('polls.PDFPoll', [
            'poll' => Poll::where('id', '=', $id)->first(),
            'questions' => PollQuestions::where('poll_id', '=', $id)->get(),
            'response' => $response,
            'data' => $data
        ]); 

        // Выводим HTTP-заголовки
        $writer = $pdf;
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        echo json_encode('data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($xlsData));
    }
}
