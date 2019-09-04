<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;
use Illuminate\Support\Facades\Validator;

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

        // $suslik = Suslik::where('uuid', '=' , $request->suslik_uuid)->first()->toArray();

        // return $this->sendResponse($suslik, 'Суслик');
    }
}
