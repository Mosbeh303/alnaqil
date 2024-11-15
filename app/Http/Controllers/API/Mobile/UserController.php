<?php

namespace App\Http\Controllers\API\Mobile;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function edit()
    {
        try {
            $user = Auth::user();
            $response = [
                'status' => true,
                'message' => "Edit profile",
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'username' => $user->username,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ],

                ],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|numeric|digits:10',
                'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
                'password' => ['nullable', Rules\Password::min(6)]
            ]);

            if ($request->avatar) {
                $fileName = time() . '.' . $request->avatar->extension();

                $request->avatar->move(public_path('uploads/avatars'), $fileName);

                $user->update([
                    'avatar' => $fileName,


                ]);
            }
            if ($request->password) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,


            ]);


            $response = [
                'status' => true,
                'message' => trans('Account_setting_updated_successfully'),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }
}
