<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\User;
use App\Rating;
use App\Region;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use libphonenumber;
use Illuminate\Validation\Rule;

class AuthController extends ApiBaseController
{
    public $successStatus = 200;
    
    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login(Request $request) { 

        $validator = Validator::make($request->all(), [ 
            'login' => 'required|min:6', 
            'password' => 'required', 
        ]);

        if ($validator->fails()) { 
            return $this->sendError("Validation error", $validator->errors()->first(), 401);      
        }

        if (filter_var(request('login'), FILTER_VALIDATE_EMAIL)) // ЛОГИН ПОЧТА
        {
            $user = User::whereRaw('email = "' . request('login') . '"')->get()->first();
        }
        if (!filter_var(request('login'), FILTER_VALIDATE_EMAIL)) // если ЛОГИН НЕ ПОЧТА
        {
            $phone = request('login');

            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');
            $phone = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

            $user = User::whereRaw('phone = "' . $phone . '"')->get()->first();
        }

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
            'uid' => 'required|uuid',
            'sex' => [
                'required',
                Rule::in(['мужской', 'женский']),
            ],
            'education' => [
                'required',
                Rule::in(['высшее или неполное высшее', 'среднее (профессиональное)', 'среднее (полное)', 'среднее (общее)', 'начальное']),
            ],
            'region' => 'required',
            'city_type' => 'required',
            'name' => 'required'
        ]);
        
        $validator->after(function ($validator) {
            if (!request('email') && !request('phone')) {
                $validator->errors()->add('field', 'Введите электронную почту или телефон');
            }
        });

        if ($validator->fails()) { 
            return $this->sendError("Validation error", $validator->errors()->first(), 401);          
        }

        $input = $request->all(); 

        if (!request('email')) // пришел ТЕЛЕФОН
        {
            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($input['phone'], 'RU');
            $input['phone'] = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
            $input['email'] = NULL;

            $validator = Validator::make($input, [ 
                'phone' => 'required|min:11|unique:users', 
            ]);
    
            if ($validator->fails()) { 
                return $this->sendError("Validation error", $validator->errors()->first(), 401);       
            }
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
            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($input['phone'], 'RU');
            $input['phone'] = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

            $validator = Validator::make($input, [ 
                'phone' => 'required|min:11|unique:users', 
            ]);
    
            if ($validator->fails()) { 
                return $this->sendError("Validation error", $validator->errors()->first(), 401);          
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
            'sex' => $input['sex'],
            'education' => $input['education'],
            'region' => $input['region'],
            'city_type' => $input['city_type'],
            'city' => $input['city'],
            'name' => $input['name'],
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

    public function getRegions() 
    { 
        $regions = Region::select('*')->orderBy('id', 'asc')->get();
        return $this->sendResponse(
            $regions->ToArray(),
            'Regions returned');
    } 
}
