<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use App\Rating;
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

    public function getUserByUid()
    {
        $user = User::where('uid', '=', request('uid'))->first();
        $user->toArray();
        $user['like'] = $user->rating()->like;
        $user['dislike'] = $user->rating()->dislike;
        $user['neutral'] = $user->rating()->neutral;
    
        if(!is_null($user))
        {
            return $this->sendResponse([
                $user
            ],
                'User returned');
        }
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uid' => 'required', 
            'name' => 'required', 
            'city' => 'required', 
            'field_of_activity' => 'required',
            'organization' => 'required',
            'position' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::where('uid', '=', request('uid'))
            ->update(['name' => $request->name, 'city' => $request->city, 'field_of_activity' => $request->field_of_activity,
            'organization' => $request->organization, 'position' => $request->position]);

        if($user > 0)
        {
            return $this->sendResponse([
                $user
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function changePhoto()
    {
        Storage::put($uid . '.jpg', $contents);
    }
}
