<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use ZipArchive;
use SplFileInfo;

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
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.susliks.create')
                ->withErrors($validator)
                ->withInput();
        }

        Suslik::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'place_of_work' => $request->place_of_work,
            'position' => $request->position,
            'category' => $request->category
        ]);

        return redirect()->route('auth.susliks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCategory()
    {
        return view('susliks.createCategory', [
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
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.auth.createCategory')
                ->withErrors($validator)
                ->withInput();
        }

        SusliksCategory::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
        ]);

        return redirect()->route('auth.susliks.index');
    }

    /**
     * Загружает сусликов из zip-папки.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadSusliks(Request $data)
    {
        $file = $data->file('file');
        $path = storage_path() . '/app/' . $file->store('temp');
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo(storage_path() . '/app/susliks_upload');
            $zip->close();
            return self::storeSusliksFromZip();
        }
        else return 'bad';
    }

    /**
     * Добавляет сусликов в БД и чистит папку.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeSusliksFromZip()
    {
        $files = scandir(storage_path() . '/app/susliks_upload');
        foreach($files as $file)
        {
            $fileType = new SplFileInfo($file);
            if($fileType->getExtension() == "csv")
            {
                $url = storage_path() . '/app/susliks_upload/' . $file;
                $handle = fopen($url, "r");
                $header = true;

                while ($csvLine = fgetcsv($handle, 10000, ";")) {

                    if ($header) {
                        $header = false;
                    } else {
                        Suslik::create([
                            'uuid' => (string) Str::uuid(),
                            'name' => $csvLine[0],
                            'place_of_work' => $csvLine[1],
                            'position' => $csvLine[2],
                            'category' => $csvLine[3],
                            'photo' => $csvLine[4],
                        ]);
                    }
                }
            }
        }
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
