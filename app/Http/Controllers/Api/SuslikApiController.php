<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
use App\User;
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
        'dislikes', 'neutrals', 'photo'])->toArray();

        if($suslik == null)
        {
            return $this->sendError(0, 'Ошибка');
        }

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

        DB::beginTransaction();
            $record = new SuslikRatingHistory;
            $record->from_suslik = auth('api')->user()->id; // от кого
            $record->whom_suslik = $is_whom->id; // кому
            $record->type = $request->input('type');
            $record->save();

            $record = Suslik::where('uuid', '=', $request->suslik_uuid)->lockForUpdate()->first();
            $record->increment($request->type);
            $record->save();
        DB::commit();

        $newRating = Suslik::where('uuid', '=' , $request->suslik_uuid)->first($request->type)->toArray();

        return $this->sendResponse($suslik, 'Суслик');
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
}
