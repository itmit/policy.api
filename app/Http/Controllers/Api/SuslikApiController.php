<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
use App\User;
use App\Favorite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SuslikApiController extends ApiBaseController
{
    public function getCategoryList()
    {
        $categorys = SusliksCategory::all('uuid', 'name')->toArray();

        return $this->sendResponse($categorys, 'Список категорий');
    }

    public function getSubCategoryList()
    {
        // $categorys = SusliksCategory::all('uuid', 'name')->toArray();

        // return $this->sendResponse($categorys, 'Список категорий');
    }

    public function getSusliksByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'category_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $cat = SusliksCategory::where('uuid', '=', $request->category_uuid)->first('id');

        $susliks = Suslik::where('category', '=' , $cat->id)->get(['uuid', 'name', 'place_of_work', 'position', 'photo'])->toArray();

        return $this->sendResponse($susliks, 'Список категорий');
    }

    public function getSuslikByID(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'suslik_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $suslik = Suslik::where('uuid', '=' , $request->suslik_uuid)->first(['id', 'uuid', 'name', 'place_of_work', 'position', 'likes', 
        'dislikes', 'neutrals', 'photo', 'category', 'link'])->toArray();

        if($suslik == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $mark = SuslikRatingHistory::where('from_suslik', '=', auth('api')->user()->id)
                            ->where('whom_suslik', '=', $suslik['id'])
                            ->latest()->first(['created_at', 'type']);

        if($mark != NULL)
        {
            $date = date_create();
            $current_date_unix = date_format($date, 'Y-m-d');
    
            $lastRateDate = $mark->created_at;
            $lastRateDate = date_format($lastRateDate, 'Y-m-d');
    
            if($current_date_unix == $lastRateDate)
            {
                $suslik['mark'] = $mark->type;
            }
            else
            $suslik['mark'] = null;
        }
        else
            $suslik['mark'] = null;

        $suslik['category'] = SusliksCategory::where('id', '=', $suslik['category'])->first(['name', 'uuid'])->toArray();
        
        return $this->sendResponse($suslik, 'Суслик');
    }

    public function rateSuslik(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'suslik_uuid' => 'required|uuid',
            'type' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $is_from = User::where('id', '=', auth('api')->user()->id)->first('id');
        if($is_from == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $is_whom = Suslik::where('uuid', '=', $request->suslik_uuid)->first();
        if($is_whom == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $canRate = SuslikRatingHistory::where('from_suslik', '=', auth('api')->user()->id)
                            ->where('whom_suslik', '=', $is_whom->id)
                            ->latest()->first('created_at');

        if($canRate != NULL)
        {
            $date = date_create();
            $current_date_unix = date_format($date, 'Y-m-d');
    
            $lastRateDate = $canRate['created_at'];
            $lastRateDate = date_format($lastRateDate, 'Y-m-d');
    
            // return 'cur: ' . $current_date_unix . ' last rate: ' . $lastRateDate;
    
            if($current_date_unix > $lastRateDate)
            {
                DB::beginTransaction();
                try {
                    $record = new SuslikRatingHistory;
                    $record->from_suslik = auth('api')->user()->id; // от кого
                    $record->whom_suslik = $is_whom->id; // кому
                    $record->type = $request->input('type');
                    $record->save();
    
                    $record = Suslik::where('uuid', '=', $request->suslik_uuid)->lockForUpdate()->first();
                    $record->increment($request->type);
                    $record->save();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->sendError(0, 'Transaction error');
                }
                $newRating = Suslik::where('uuid', '=' , $request->suslik_uuid)->first($request->type)->toArray();
    
                return $this->sendResponse($newRating, 'Суслик');
            }
            else
            {
                return $this->sendError(0, 'Нельзя голосовать чаще одного раза в сутки');
            }
            return $this->sendError(0, 'Неизвестная ошибка');
        }
        else
        {
            DB::beginTransaction();
            try {
                $record = new SuslikRatingHistory;
                $record->from_suslik = auth('api')->user()->id; // от кого
                $record->whom_suslik = $is_whom->id; // кому
                $record->type = $request->input('type');
                $record->save();

                $record = Suslik::where('uuid', '=', $request->suslik_uuid)->lockForUpdate()->first();
                $record->increment($request->type);
                $record->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError(0, 'Transaction error');
            }
            $newRating = Suslik::where('uuid', '=' , $request->suslik_uuid)->first($request->type)->toArray();

            return $this->sendResponse($newRating, 'Суслик');
        }

        

        
    }

    public function getSuslikRatingHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'suslik_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $suslik_id = Suslik::where('uuid', '=', $request->suslik_uuid)->first('id');
        if($suslik_id == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $suslikRatingHistory = SuslikRatingHistory::where('whom_suslik', '=', $suslik_id->id)->get()->toArray();

        return $this->sendResponse($suslikRatingHistory, 'Суслик');
    }

    public function getFavsList(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'user_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::where('uid', '=', $request->user_uuid)->first('id');
        if($user == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $favsList = Favorite::where('user_id', '=', $user->id)->get()->toArray();

        $response = [];

        foreach($favsList as $favList)
        {
            // $user = User::where('id', '=', $favsList->user_uuid)->first(['uid', 'name']);
            $suslik = Suslik::where('id', '=', $favList['suslik_id'])->first(['uuid', 'name', 'photo']);
            if(!$suslik)
            {
                continue;
            }
            $response[] = 
            [
                'uuid' => $suslik->uuid,
                'name' => $suslik->name,
                'photo' => $suslik->photo,
            ];
        }

        return $this->sendResponse($response, 'Список избранных');
    }

    public function addToFav(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'suslik_uuid' => 'required|uuid',
            'user_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $suslik_id = Suslik::where('uuid', '=', $request->suslik_uuid)->first('id');
        if($suslik_id == null)
        {
            return $this->sendError('Error', 'Ошибка');
        }

        $user_id = User::where('uid', '=', $request->user_uuid)->first('id');
        if($user_id == null)
        {
            return $this->sendError('Error', 'Ошибка');
        }

        $already_in_fav = Favorite::where('user_id', '=', $user_id->id)->where('suslik_id', '=', $suslik_id->id)->first();

        if($already_in_fav != null)
        {
            return $this->sendResponse([0], 'Пользователь уже в избранном');
        }

        $favorite = Favorite::create([
            'user_id' => $user_id->id,
            'suslik_id' => $suslik_id->id,
        ]);

        return $this->sendResponse([$favorite], 'Добавлено в избранное');
    }

    public function removeFromFav(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'suslik_uuid' => 'required|uuid',
            'user_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $suslik_id = Suslik::where('uuid', '=', $request->suslik_uuid)->first('id');
        if($suslik_id == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $user_id = User::where('uid', '=', $request->user_uuid)->first('id');
        if($user_id == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

        $already_in_fav = Favorite::where('user_id', '=', $user_id->id)->where('suslik_id', '=', $suslik_id->id)->first();

        if($already_in_fav == null)
        {
            return $this->sendError(0, 'Пользователь не в избранном');
        }

        $favorite = Favorite::where('user_id', '=', $user_id->id)->where('suslik_id', '=', $suslik_id->id)->delete();

        return $this->sendResponse([$favorite], 'Добавлено в избранное');
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'ratingOrderBy' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $searchResponse;

        $all = true;
        $isName = false;

        if($request->category != NULL)
        {
            if($request->name != NULL)
            {
                $susliks = self::searchBySuslikCategory($request->category, $request->name); // ЕСТЬ КАТЕГОРИЯ И ЕСТЬ ИМЯ
                $searchResponse = $susliks;
                $isName = true;
                // return $this->sendResponse($searchResponse, 'name + cat');
            }
            else
            {   
                $susliks = self::searchBySuslikCategory($request->category); // ЕСТЬ КАТЕГОРИЯ НО НЕТ ИМЕНИ
                $searchResponse = $susliks; 
            }
            $all = false;
        }

        if($request->name != NULL && $isName == false)
        {
            $susliks = self::searchBySuslikName($request->name);
            $searchResponse = $susliks;
            $all = false;
        }

        if($all == true)
        {
            $susliks = Suslik::all('uuid', 'name', 'place_of_work', 'position', 'photo', 'likes')->toArray();
            $searchResponse = $susliks;
        }

        $searchResponse = self::suslikRatingOrderBy($request->ratingOrderBy, $searchResponse);

        return $this->sendResponse($searchResponse, 'Список сусликов, удовлетворяющий поисковый запрос');
    }

    public function searchBySuslikCategory(string $category, string $getName = NULL)
    {
        $cat = SusliksCategory::where('uuid', '=', $category)->first('id');

        if($getName == NULL)
        {
            $susliks = Suslik::where('category', '=' , $cat->id)->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
        }
        if($getName != NULL)
        {
            // return 'name: ' . $getName . ' category id: ' . $cat->id;
            $susliks = Suslik::where('category', '=' , $cat->id)
                ->where('name', 'LIKE', "%$getName%") // 
                ->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
        }
        return $susliks;
    }

    public function searchBySuslikName(string $name)
    {
        return $susliks = Suslik::where('name', 'LIKE', "%$name%")
            ->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
    }

    public function suslikRatingOrderBy(string $ratingOrderBy, $susliks)
    {
        $susliksSorted = [];
        if($ratingOrderBy == 'asc')
        {
            $susliksSortedBySort = collect($susliks)->sortBy('likes')->toArray(); //

            foreach($susliksSortedBySort as $key => $value)
            {
                $susliksSorted[] = $value;
            }
            
            return $susliksSorted;
        }

        if($ratingOrderBy == 'desc')
        {
            $susliksSortedBySort = collect($susliks)->sortBy('likes')->reverse()->toArray(); //
            foreach($susliksSortedBySort as $key => $value)
            {
                $susliksSorted[] = $value;
            }
            return $susliksSorted;
        }
        return 'error';
    }

    /**
     * Вывод стастистики суслика
     */
    public function showStatistic($uuid)
    {
        $suslik = Suslik::where('uuid', '=', $uuid)->first();

        if($suslik == NULL)
        {
            return 'erer';
        }

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
            $voteInDay = SuslikRatingHistory::where('whom_suslik', '=', $suslik->id)->whereBetween('created_at', [$today . " 00:00:00", $today . " 23:59:59"])->get();
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
}
