<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiBaseController
{
    public $successStatus = 200;
    
    public function index()
    {
        $users = User::all();

        return $this->sendResponse([
            $users
        ],
            'Users returned');
    }

    public function getUserByUid(Request $uid)
    {
        $user = User::where('uid', $uid)->first();

        return $this->sendResponse([
            $user
        ],
            'Users returned');
    }

}
