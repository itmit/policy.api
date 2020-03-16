<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
use App\Region;
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
            ->orderBy('created_at', 'desc')->get(),
        'categories' => SusliksCategory::get()
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
            ->orderBy('created_at', 'desc')->get(),
            'regions' => Region::select('*')
            ->orderBy('id', 'asc')->get()
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
            // 'sex' => 'required',
            // 'education' => 'required',
            // 'region' => 'required',
            // 'birthday' => 'required',
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
            'number' => $request->number,
            // 'sex' => $request->sex,
            // 'education' => $request->education,
            // 'region' => $request->region,
            // 'birthday' => $request->birthday
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
            'category' => 'required|exists:susliks_categories,id'
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
                    foreach(scandir($file) as $p) if (($p!='.') && ($p!='..'))
                    unlink($file.DIRECTORY_SEPARATOR.$p);
                    // return rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }

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
                    foreach(scandir($file) as $p) if (($p!='.') && ($p!='..'))
                    unlink($file.DIRECTORY_SEPARATOR.$p);
                    // return rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }
        return true;
    }

    public function uploadSusliksJSON(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'file' => 'required',
            'category' => 'required|exists:susliks_categories,id'
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
                    foreach(scandir($file) as $p) if (($p!='.') && ($p!='..'))
                    unlink($file.DIRECTORY_SEPARATOR.$p);
                    // return rmdir($file);
                }
                else
                {
                    unlink($file);
                }
            }
        }

        $file = $data->file('file');
        $path = storage_path() . '/app/' . $file->store('temp');
        $j = file_get_contents($path); // в примере все файлы в корне
        $susliks = json_decode($j);
        foreach ($susliks->politic as $suslik) {
            if(!isset($suslik->FIO)) continue;
            if(Suslik::where('name', $suslik->FIO)->exists()) continue;
            if(!isset($suslik->place_of_work)) $suslik->place_of_work = null;
            if(!isset($suslik->photo)) $suslik->photo = null;
            if(!isset($suslik->position)) $suslik->position = null;
            if(!isset($suslik->birthdate)) $suslik->birthdate = null;
            $link = explode(' ', $suslik->FIO);
            if(!isset($link[2]))
            {
                $link[2] = $link[1];
                $link[1] = '';
            };

            $contents = file_get_contents($suslik->photo);
            $name = substr($suslik->photo, strrpos($suslik->photo, '/') + 1);
            $path = Storage::put('public/susliks/'.$name, $contents);

            $suslik = Suslik::create([
                'uuid' => (string) Str::uuid(),
                'name' => $suslik->FIO,
                'birthdate' => $suslik->birthdate,
                'position' => $suslik->position,
                'place_of_work' => $suslik->place_of_work,
                'position' => $suslik->position,
                'category' => $data->category,
                'link' =>'https://ru.wikipedia.org/wiki/' . $link[2] . ',_' . $link[0] . '_' . $link[1],
                'photo' => $path->pathname
            ]);
        }

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
        $today = date("Y-m-d");
        $day = date("Y-m-d H:i:s");
        
        $i = 1;
        $max = 0;
        $votes = [];
        while ($i <= 7) {
            $key = date("d.m", strtotime($day));
            $voteDetail = [];
            $likes = 0;
            $neutrals = 0;
            $dislikes = 0;
            $count = 0;
            $voteInDay = SuslikRatingHistory::where('whom_suslik', '=', $id)->whereBetween('created_at', [$today . " 00:00:00", $today . " 23:59:59"])->get();
            foreach($voteInDay as $item)
            {
                $count++;
                switch ($item->type) {
                    case 'likes':
                        $likes++;
                        break;
                    case 'neutrals':
                        $neutrals++;
                        break;
                    case 'dislikes':
                        $dislikes++;
                        break;
                }
            }
            if($count > 0)
            {
                $likes = $likes / $count * 100;
                $neutrals = $neutrals / $count * 100;
                $dislikes = $dislikes / $count * 100;
            }
            
            $voteDetail['likes'] = $likes;
            $voteDetail['neutrals'] = $neutrals;
            $voteDetail['dislikes'] = $dislikes;
            $voteDetail['count'] = $count;
            $votes[$key] = $voteDetail;
            if($max < $voteDetail['count'])
            {
                $max = $voteDetail['count'];
            }
            if($i == 1)
            {
                $day = date("d.m.Y", strtotime('-'.$i.' day'));
                $today = date("Y-m-d", strtotime('-'.$i.' day'));
            }
            else
            {
                $day = date("d.m.Y", strtotime('-'.$i.' days'));
                $today = date("Y-m-d", strtotime('-'.$i.' day'));
            }
            $i++;
        }

        $votes = array_reverse($votes);

        return view('statistic', [
            'votes' => $votes,
            'max' => $max,
        ]); 
    }

    public function uploadRegions()
    {
        $files = scandir(storage_path() . '/app/regions');
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
                $url = storage_path() . '/app/regions/' . $file;
                // Читаем файл и записываем информацию в переменную
                $spreadsheet = $reader->load($url);
                            
                // Так можно достать объект Cells, имеющий доступ к содержимому ячеек
                $cells = $spreadsheet->getActiveSheet()->getCellCollection();

                // return $cells->getHighestRow();
                        
                $result = [];
                $region = [];

                // Далее перебираем все заполненные строки (столбцы A - E)
                for ($row = 2; $row <= $cells->getHighestRow(); $row++){
                    // $result[] = $result[$row];
                    for ($col = 'A'; $col <= 'B'; $col++) {
                        // Так можно получить значение конкретной ячейки
                        if($region[$col] = $cells->get($col.$row) == NULL)
                        {
                            continue;
                        }
                        $region[$col] = $cells->get($col.$row)->getValue();
                    }
                    $result[$row] = $region;
                    $region = [];
                }   
                
                foreach($result as $item)
                {
                    $isSuslikExists = Region::where('id', '=', $item['A'])->first();
                    if($isSuslikExists != NULL)
                    {
                        continue;
                    }

                    $newSuslik = Region::create([
                        'uuid' => (string) Str::uuid(),
                        'name' => $item['B'],
                        'id' => $item['A'],
                    ]);
                }
            }      
        }

        return 'uploaded';
    }

    public function clearUploadSusliksDirectory()
    {

    }
}
