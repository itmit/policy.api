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
use libphonenumber;

class AuthController extends ApiBaseController
{
    public $successStatus = 200;
    
    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login() { 

        if (filter_var(request('login'), FILTER_VALIDATE_EMAIL)) // ЛОГИН ПОЧТА
        {
            $user = User::whereRaw('email = "' . request('login') . '"')->get()->first();
        }
        if (!filter_var(request('login'), FILTER_VALIDATE_EMAIL)) // если ЛОГИН НЕ ПОЧТА
        {
            $phone = request('login');
            $phoneIsValid = false;
            $firstLetter = $phone[0];
            if($firstLetter == '+')
            {
                $phone = preg_replace('~\D+~', '', $phone); 
                $phone = '+' . $phone;
                $phoneIsValid = true;
            }
            if($firstLetter == '8')
            {
                $phone = preg_replace('~\D+~', '', $phone); 
                $phone = substr($phone, 1);
                $phone = '+' . $phone;
                $phoneIsValid = true;
            }

            if($phoneIsValid == true)
            {
                $user = User::whereRaw('phone = "' . $phone . '"')->get()->first();
            }
            else
            {
                return $this->SendError('Authorization error', 'Something wrong with phone number', 401);
            }
        }

        // $user = User::whereRaw('email = "' . request('login') . '" or phone = "' . request('login') . '"')
        // ->get()->first();

        if ($user != null) {
            if (Hash::check(request('password'), $user->password))
            {
                Auth::login($user);
            }
            else
            {
                return $this->SendError('Authorization error', 'Wrong password', 401);
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
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'password' => 'required|min:6', 
            'c_password' => 'required|same:password', 
            'birthday' => 'required|date_format:Y-m-d', 
            'uid' => 'required',
        ]);
        
        $validator->after(function ($validator) {
            if (!request('email') && !request('phone')) {
                $validator->errors()->add('field', 'Something is wrong with this field!');
            }
        });

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 

        if (!request('email')) // пришел ТЕЛЕФОН
        {
            // $firstLetter = $input['phone'][0];
            // if($firstLetter == '+')
            // {
            //     $input['phone'] = preg_replace('~\D+~', '', $input['phone']); 
            //     $input['phone'] = '+' . $input['phone'];
            // }
            // else
            // {
            //     return $this->SendError('Authorization error', 'Phone number is not valid', 401);
            // }
            // $input['email'] = NULL;
            return $phoneNumberUtil->format($phone, PhoneNumberFormat::E164);
        }

        if (!request('phone')) // пришел ЕМАИЛ
        {
            if (!filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
                return $this->SendError('Authorization error', 'Email is not an Email', 401);
            }
            $input['phone'] = NULL;
        }

        if(request('phone') && request('email')) // если пришли оба
        {
            $firstLetter = $input['phone'][0];
            if($firstLetter == '+')
            {
                $input['phone'] = preg_replace('~\D+~', '', $input['phone']); 
                $input['phone'] = '+' . $input['phone'];
            }
            else
            {
                return $this->SendError('Authorization error', 'Phone number is not valid', 401);
            }

            if (!filter_var(request('email'), FILTER_VALIDATE_EMAIL)) {
                return $this->SendError('Authorization error', 'Email is not an Email', 401);
            }
        }
 
        $tryRegister = User::WhereRaw('email = "' . $input['email'] . '" or phone = "' . $input['phone'] . '" or uid = "' . $input['uid'] . '" and phone <> "' . $input['phone'] . '"')->first();

        if($tryRegister)
        {
            return $this->SendError('Authorization error', 'User already exist', 401);
        }

        $user = User::create([
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => bcrypt($input['password']),
            'birthday' => $input['birthday'],
            'uid' => $input['uid'],
        ]);

        Auth::login($user);     

        if (Auth::check()) {
            $tokenResult = $user->createToken(config('app.name'));
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $user_rating = Rating::create([
                'user_id' => Auth::id(),
            ]);

            return $this->sendResponse([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ],
                'Authorization is successful');
        }
        
        return $this->SendError('Authorization error', 'Unauthorised', 401);
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return $this->sendResponse(
            $user->ToArray(),
            'Details returned');
    } 
}
