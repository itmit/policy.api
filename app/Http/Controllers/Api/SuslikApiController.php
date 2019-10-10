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

        $suslik = Suslik::where('uuid', '=' , $request->suslik_uuid)->first(['uuid', 'name', 'place_of_work', 'position', 'likes', 
        'dislikes', 'neutrals', 'photo', 'category'])->toArray();

        if($suslik == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

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
            $suslik = Suslik::where('id', '=', $favList['suslik_id'])->first(['uuid', 'name']);
            $response[] = 
            [
                'uuid' => $suslik->uuid,
                'name' => $suslik->name
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
            return $this->sendError([0], 'Ошибка');
        }

        $user_id = User::where('uid', '=', $request->user_uuid)->first('id');
        if($user_id == null)
        {
            return $this->sendError([0], 'Ошибка');
        }

        $already_in_fav = Favorite::where('user_id', '=', $user_id->id)->where('suslik_id', '=', $suslik_id->id)->first();

        if($already_in_fav != null)
        {
            return $this->sendError([0], 'Пользователь уже в избранном');
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

        if($request->category != NULL)
        {
            if($request->name != NULL)
            {
                $susliks = self::searchBySuslikCategory($request->category, $request->name);
                $searchResponse = $susliks;
            }
            else
            {   
                $susliks = self::searchBySuslikCategory($request->category);
                $searchResponse = $susliks; 
            }
            $all = false;
        }

        if($request->name != NULL)
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
        $susliks = Suslik::where('category', '=' , $cat->id)->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
        
        if($getName != NULL)
        {
            $susliks = Suslik::where('category', '=' , $cat->id)
                ->where('name', 'LIKE', "%$getName%")
                ->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
        }
        return $susliks;
    }

    public function searchBySuslikName(string $name)
    {
        $susliks = Suslik::where('name', 'LIKE', "%$name%")
            ->get(['uuid', 'name', 'place_of_work', 'position', 'photo', 'likes'])->toArray();
    }

    public function suslikRatingOrderBy(string $ratingOrderBy, array $susliks)
    {
        if($ratingOrderBy == 'asc')
        {
            return $susliks;
            $susliksSorted = collect($susliks)->sortBy('likes','ASC')->toArray();
            return $susliksSorted;
        }
        if($ratingOrderBy == 'desc')
        {
            return $susliks;
            $susliksSorted = collect($susliks)->sortBy('likes','DESC')->toArray();
            return $susliksSorted;
        }
        return 'error';
    }
}
