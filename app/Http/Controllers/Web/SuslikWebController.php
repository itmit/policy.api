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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\MyReadFilter;

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
            'link' => 'required|min:3|max:191',
            'number' => 'required|unique:susliks',
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
            'category' => $request->category,
            'link' => $request->link,
            'number' => $request->number
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
        $validator = Validator::make($data->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('auth.susliks.index')
                ->withErrors($validator)
                ->withInput();
        }

        $path = storage_path() . '/app/temp';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                unlink($file);
            }
        }

        $path = storage_path() . '/app/susliks_upload';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                if(is_dir($file))
                {
                    rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }

        $file = $data->file('file');
        $path = storage_path() . '/app/' . $file->store('temp');
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo(storage_path() . '/app/susliks_upload');
            $zip->close();
            $import = self::storeSusliksFromZip();
        }
        else return 'bad';
        return redirect()->route('auth.susliks.index');
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

            // ...
            // Создаём ридер 
            $reader = new Xlsx();
            // Если вы не знаете, какой будет формат файла, можно сделать ридер универсальным:
            // $reader = IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
           
            if($fileType->getExtension() == "xlsx")
            {
                $url = storage_path() . '/app/susliks_upload/' . $file;
                // Читаем файл и записываем информацию в переменную
                $spreadsheet = $reader->load($url);
                            
                // Так можно достать объект Cells, имеющий доступ к содержимому ячеек
                $cells = $spreadsheet->getActiveSheet()->getCellCollection();

                // return $cells->getHighestRow();
                        
                $result = [];
                $suslik = [];

                // Далее перебираем все заполненные строки (столбцы A - E)
                for ($row = 2; $row <= $cells->getHighestRow(); $row++){
                    // $result[] = $result[$row];
                    for ($col = 'A'; $col <= 'G'; $col++) {
                        // Так можно получить значение конкретной ячейки
                        if($suslik[$col] = $cells->get($col.$row) == NULL)
                        {
                            continue;
                        }
                        $suslik[$col] = $cells->get($col.$row)->getValue();
                    }
                    $result[$row] = $suslik;
                    $suslik = [];
                }   
                
                foreach($result as $item)
                {
                    $categoryID = SusliksCategory::where('name', '=', $item['E'])->first('id');
                    if($categoryID == NULL)
                    {
                        continue;
                    }

                    $isSuslikExists = Suslik::where('number', '=', $item['A'])->first();
                    if($isSuslikExists != NULL)
                    {
                        continue;
                    }

                    $newSuslik = Suslik::create([
                        'uuid' => (string) Str::uuid(),
                        'name' => $item['B'],
                        'number' => $item['A'],
                        'place_of_work' => $item['C'],
                        'position' => $item['D'],
                        'category' => $categoryID->id,
                        'link' => $item['G'],
                    ]);

                    foreach($files as $suslikImage)
                    { 
                        $imageName = new SplFileInfo($suslikImage);
                        if($imageName->getFilename() == $item['F'])
                        {
                            $imageExtension = $imageName->getExtension();
                            $urlImage = storage_path() . '/app/susliks_upload/' . $imageName;

                            if (file_exists($urlImage))
                            {
                                $photo = $newSuslik->uuid;
                                rename($urlImage, storage_path() . '/app/public/susliks/' . $photo . '.' . $imageExtension);
                                
                                Suslik::where('id', '=', $newSuslik->id)->update([
                                    'photo' => $photo . '.' . $imageExtension
                                ]);  
                            }                          
                        }
                    }
                }
            }      
        }

        $path = storage_path() . '/app/temp';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                unlink($file);
            }
        }

        $path = storage_path() . '/app/susliks_upload';
        if (file_exists($path)) {
            foreach (glob($path.'/*') as $file) {
                if(is_dir($file))
                {
                    rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }
        return true;
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
    public function destroy(Request $request)
    {
        Suslik::destroy($request->input('ids'));

        return response()->json(['Places destroyed']);
    }

    /**
     * Тест
     */
    public function showStatistic($id)
    {
        $today = date("Y-m-d H:i:s");
        $inSevenDays =  date('Y-m-d H:i:s', strtotime('-1 week'));
        $lastSevenDays = SuslikRatingHistory::where('whom_suslik', '=', $id)->whereBetween('created_at', [$today, $inSevenDays])->get();
        dd($lastSevenDays);
        return view('statistic', [
            'suslik' => Suslik::where('id', '=', $id)->first(),
            'history' => SuslikRatingHistory::where('whom_suslik', '=', $id)->get()
        ]); 
    }
    
}
