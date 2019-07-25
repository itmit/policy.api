<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class UserController extends ApiBaseController
{
    public $successStatus = 200;
    
    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login() { 
        $user = User::whereRaw('email = "' . request('login') . '" or phone = "' . request('login') . '"')
        ->get()->first();

        if ($user != null) {
            if (Hash::check(request('password'), $user->password))
            {
                Auth::login($user);
            }

            if (Auth::check()) {
                $tokenResult = $user->createToken(config('app.name'));
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                return $this->sendResponse([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ],
                    'Authorization is successful');
            }
        }

        return $this->SendError('Authorization error', 'Unauthorised', 401);
    }

    /** 
     * Register api 
     * 
     * @return Response 
     */ 
    public function register_create(Request $request) 
    { 
        // $validator = Validator::make($request->all(), [ 
        //     'name' => 'required', 
        //     'email' => 'required|email', 
        //     'password' => 'required', 
        //     'c_password' => 'required|same:password', 
        // ]);

        // if ($validator->fails()) { 
        //     return response()->json(['error'=>$validator->errors()], 401);            
        // }

        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']);
 
        $user = User::create([
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => bcrypt($input['password']),
            'name' => $input['name'],
            'city' => $input['city'],
            'field_of_activity' => $input['field_of_activity'],
            'organization' => $input['organization'],
            'position' => $input['position'],
            'birthday' => $input['birthday'],
        ]);

        // $success['token'] =  $user->createToken('MyApp')->accessToken; 
        // $success['name'] =  $user->name;

        // return response()->json(['success' => $success], $this->successStatus); 

        // return $this->sendResponse(['suc' ] ,'Authorization is successful');
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this->successStatus); 
    } 
}
