<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
use Illuminate\Support\Str;

class SuslikWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('susliks.susliks', [
        'susliks' => Suslik::select('*')
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
        return view('susliks.suslikCreate', [
            'categories' => SusliksCategory::select('*')
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:191',
            'place_of_work' => 'required|min:3|max:191',
            'position' => 'required|min:3|max:191',
            'category' => 'required|min:3|max:191',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation error", 401);
        }

        Suslik::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'place_of_work' => $request->place_of_work,
            'position' => $request->position,
            'category' => $request->category
        ]);

        return redirect()->route('auth.susliks.index');
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
