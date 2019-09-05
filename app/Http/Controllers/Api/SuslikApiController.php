<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use App\SuslikRatingHistory;
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

        $susliks = Suslik::where('category', '=' , $cat->id)->get()->toArray();

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

        $suslik = Suslik::where('uuid', '=' , $request->suslik_uuid)->first()->toArray();

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

        $is_from = Suslik::where('id', '=', auth('api')->user()->id)->first();
        $is_whom = Suslik::where('uuid', '=', $request->suslik_uuid)->first();

        return $is_from->id;

        DB::beginTransaction();
            $record = new SuslikRatingHistory;
            $record->from_suslik = auth('api')->user()->id; // от кого
            $record->whom_suslik = $request->input('suslik_uuid'); // кому
            $record->type = $request->input('type');
            $record->save();

            $record = Suslik::where('uuid', '=', $request->suslik_uuid)->lockForUpdate()->first();
            $record->increment($request->type);
            $record->save();
        DB::commit();

        // return auth('api')->user()->id;

        // $suslik = Suslik::where('uuid', '=' , $request->suslik_uuid)->first()->toArray();

        // return $this->sendResponse($suslik, 'Суслик');
        return 'Suc';
    }
}
