<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SusliksCategory;
use App\Suslik;

class SuslikApiController extends ApiBaseController
{
    public function getCategoryList()
    {
        $categorys = SusliksCategory::all('id', 'name')->toArray();

        return $this->sendResponse($categorys, 'Список категорий');
    }

    public function getSusliksByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'category_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $susliks = Suslik::where('category', '=' , $request->category_id)->get();

        return $this->sendResponse($susliks, 'Список категорий');
    }
}
