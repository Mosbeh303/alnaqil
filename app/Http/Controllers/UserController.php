<?php

namespace App\Http\Controllers;


use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Auth ;


class UserController extends Controller
{
    public function updatePassword(Request $request, User $user){

        //$this->authorize('isEmployeeHasPermission', 'edit_operators');

        $request->validate([
            'password' => ['required', Rules\Password::min(6)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect::back()->with('success', trans('password_updated_successfully'));
    }


    public function updateUsername(Request $request, User $employee){
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$employee->username,
        ]);

        $employee->update([
            'username' =>$request->username,
        ]);

        return redirect::back()->with('success', trans('employe_uername_updated_successfully'));
    }







    public function updateAvatar(Request $request, User $user){
        $request->validate([
            'avatar' => 'required|mimes:png,jpeg,jpg|max:2048',        ],
        [
            'avatar.required' => trans('picture_is_required')
        ]);

        if ($request->avatar) {
            $fileName = time().'.'.$request->avatar->extension();

            $request->avatar->move(public_path('uploads/avatars'), $fileName);
        }

        $user->update([
            'avatar' => $fileName,
        ]);


        return Redirect::back()->with('success', trans('file_added'));
    }

    public function edit(){
        $user = Auth::user();
        $store = $user->store ;
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ],
            'store' => $store ? [
                'id' => $store->id,
                'userId' => $store->user_id,
                'name' => $store->name,
                'status' => $store->status,
                'username' => $store->user->username,
                'ownerName' => $store->user->name,
                'phone' => $store->user->phone,
                'email' => $store->user->email,
                'city' => $store->city,
                'neighborhood' => $store->neighborhood,
                'govNumber' => $store->gov_number,
                'taxNumber' => $store->tax_number,
                'maaroof' => $store->maaroof,
                'freelanceDocument' => $store->freelance_document,
                'baseFee' => $store->base_fee,
                'baseFee2' => $store->base_fee2,
                'baseFee3' => $store->base_fee3,
                'returnedFee' => $store->returned_fee,
                'refrigeratedFee' => $store->refrigerated_transport_fee,
                'exchangeFee' => $store->exchange_fee,
                'returnFee' => $store->return_fee,
                'codFee' => $store->cod_fee,
                'fixedAmountCodFee' => $store->fixed_amount_cod_fee,
                'codFeePercent' => $store->cod_fee_percent,
                'website' => $store->website,
                'bank' => $store->bankAccount()->exists() ? $store->bankAccount->bank_name : null,
                'accountOwner' =>  $store->bankAccount()->exists() ? $store->bankAccount->owner_name : null,
                'rib' => $store->bankAccount()->exists() ? $store->bankAccount->rib : null,
            ] : null,
            'cities' => City::where('type', 'register')->get() ?? [trans('alriyadh')],
            'addresses' => $store ? $store->pickupAddresses()->get() : null ,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'username' => 'required|string|max:255|unique:users,username,'.$user->username,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ],
        );


        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
        ]);

        return redirect::back()->with('success', trans('Account_setting_updated_successfully'));
    }



    public function api(){

        $webhook = Auth::user()->webhook ;

        if (!$webhook){
            $webhook = Auth::user()->webhook()->create([
                'url' => null,
                'secret' => \Illuminate\Support\Str::random()
            ]);
        }

        return Inertia::render('Users/Api', [
            'webhook' => [
                'active' => $webhook->active == 1 ? true : false,
                'id' =>  $webhook->id,
                'user_id' =>  $webhook->user_id,
                'url' =>  $webhook->url,
                'secret' =>  $webhook->secret,
                'created_at' =>  $webhook->created_at,
            ],
            'accessToken'  => Auth::user()->tokens()->get()[0] ?? null,
        ]);
    }

    public function generateToken(){
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('apitoken')->plainTextToken;
        return $token;

    }


}
