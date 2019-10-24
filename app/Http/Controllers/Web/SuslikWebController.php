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
        // dd($files);
        foreach($files as $file)
        {
            $fileType = new SplFileInfo($file);

            // ...
            // Создаём ридер 
            $reader = new Xlsx();
            // Если вы не знаете, какой будет формат файла, можно сделать ридер универсальным:
            // $reader = IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            
            // Если вы хотите установить строки и столбцы, которые необходимо читать, создайте класс ReadFilter
            // $reader->setReadFilter( new MyReadFilter(11, 1000, range('B', 'O')) );
            if($fileType->getExtension() == "xlsx")
            {
                $url = storage_path() . '/app/susliks_upload/' . $file;
                // Читаем файл и записываем информацию в переменную
                $spreadsheet = $reader->load($url);
                            
                // Так можно достать объект Cells, имеющий доступ к содержимому ячеек
                $cells = $spreadsheet->getActiveSheet()->getCellCollection();

                return $cells->getHighestRow();
                        
                // Далее перебираем все заполненные строки (столбцы B - O)
                for ($row = 0; $row <= $cells->getHighestRow(); $row++){
                    for ($col = 'A'; $col <= 'E'; $col++) {
                        // Так можно получить значение конкретной ячейки
                        return $cells->get($col.$row)->getValue();

                        // а также здесь можно поместить ваш функциональный код
                    }
                }            
            }      

            // if($fileType->getExtension() == "csv" || $fileType->getExtension() == "xlsx")
            // {
            //     $url = storage_path() . '/app/susliks_upload/' . $file;
            //     $handle = fopen($url, "r");
            //     $header = true;

            //     while ($csvLine = fgetcsv($handle, 10000, ";")) {

            //         if ($header) {
            //             $header = false;
            //         } else {
            //             $categoryID = SusliksCategory::where('name', '=', $csvLine[3])->first('id');
            //             if($categoryID == NULL)
            //             {
            //                 continue;
            //             }
            //             $newSuslik = Suslik::create([
            //                 'uuid' => (string) Str::uuid(),
            //                 'name' => $csvLine[0],
            //                 'place_of_work' => $csvLine[1],
            //                 'position' => $csvLine[2],
            //                 'category' => $categoryID->id,
            //                 'photo' => $csvLine[4],
            //             ]);

            //             foreach($files as $suslikImage)
            //             { 
            //                 $imageName = new SplFileInfo($suslikImage);
            //                 if($imageName->getFilename() == $csvLine[4])
            //                 {
            //                     $imageExtension = $imageName->getExtension();
            //                     $urlImage = storage_path() . '/app/susliks_upload/' . $imageName;
            //                     $photo = $newSuslik->uuid;
            //                     rename($urlImage, storage_path() . '/app/public/susliks/' . $photo . '.' . $imageExtension);
                                
            //                     Suslik::where('id', '=', $photo)->update([
            //                         'photo' => $photo . '.' . $imageExtension
            //                     ]);
            //                 }
            //             }
            //         }
            //     }
            //     unlink($url);
            // }
        }

        // $path = storage_path() . '/app/temp';
        // if (file_exists($path)) {
        //     foreach (glob($path.'/*') as $file) {
        //         unlink($file);
        //     }
        // }
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
