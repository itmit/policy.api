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
        $categorys = SusliksCategory::select('id', 'name');

        return $this->sendResponse($categorys, 'Список категорий');
    }
}
