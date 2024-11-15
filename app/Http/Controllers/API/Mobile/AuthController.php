<?php

namespace App\Http\Controllers\API\Mobile;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use App\Models\Setting;
use Log;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */

     public function save_token(Request $request)
     {
         try {
             if (Auth::check()) {

                $request->validate([
                    'device_token' => 'required',
                ]);

                 $user = Auth::user();
                 $existingToken = $user->devicesTokens()->where('value', $request->device_token)->first();

                 $language = $request->get('language') === 1 ? 'ar' : 'en';

                 Log::info($language);

                 if (!$existingToken) {
                  $deviceToken =    $user->devicesTokens()->create([
                         'value' => $request->device_token,
                         'user_id' => $user->id,
                         'language' =>   $language ,
                     ]);

                     return response()->json([
                         'status' => true,
                         'message' => 'Token added with success',
                     ], 201);
                 } else {
                     $existingToken->update([
                        'language' =>   $language ,
                     ]);
                     return response()->json([
                         'status' => false,
                         'message' => 'Token already exists',
                     ], 201);
                 }
             } else {
                 return response()->json([
                     'status' => false,
                     'message' => 'User not authenticated',
                 ], 401);
             }
         } catch (\Exception $e) {
             return response()->json([
                 'status' => false,
                 'message' => 'An error occurred: ' . $e->getMessage(),
             ], 500);
         }
     }



    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric|digits:10',
                'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
                'password' => ['required', Rules\Password::min(6)]
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }



            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'username' => $request->username,
            ]);



            $base_fee = floatval(Setting::where('option', 'base_fee')->value('value'));
            $exchange_fee = floatval(Setting::where('option', 'exchange_fee')->value('value'));
            $refrigerated_transport_fee = floatval(Setting::where('option', 'refrigerated_transport_fee')->value('value'));
            $return_fee = floatval(Setting::where('option', 'return_fee')->value('value'));

            $user->store()->create([
                'name' => $request->name,
                // 'city' => $request->city,
                // 'neighborhood' => $request->neighborhood,
                'base_fee' => $base_fee,
                'exchange_fee' => $exchange_fee,
                'refrigerated_transport_fee' => $refrigerated_transport_fee,
                'extra_fees' => $return_fee,
                'type' => 'individual',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'user' => [
                   'name' => $user->name,
                   'email' => $user->email
                ],
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();


            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' => [
                   'name' => $user->name,
                   'email' => $user->email
                ],
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
