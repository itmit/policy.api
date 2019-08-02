<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use App\Rating;
use App\Feedback;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

    public function changePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uid' => 'required', 
            'contents' => 'image|mimes:jpeg,jpg,png,gif|required|max:10000',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        // $path = $request->file('contents')->store('public/avatars');
        // $url = Storage::url($path);
        // Storage::put($request->uid . '.jpg', $request->contents);

        $path = Storage::putFileAs(
            'public/avatars', $request->file('contents'), $request->uid . '.jpg'
        );

        $user = User::where('uid', '=', $request->uid)
            ->update(['photo' => $path]);

        if($user > 0)
        {
            return $this->sendResponse([
                $user
            ],
                'Updated');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }

    public function sendFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uid' => 'required', 
            'title' => 'required|max:250',
            'category' => 'required|max:250', 
            'message' => 'required|max:10000', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = User::where('uid', '=', $request->uid)->select('id')->first();

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'category' => $request->category,
            'message' => $request->message,
        ]);

        if($feedback > 0)
        {
            return $this->sendResponse([
                $feedback
            ],
                'Added');
        }
        return $this->SendError('Update error', 'Something gone wrong', 401);
    }
}
