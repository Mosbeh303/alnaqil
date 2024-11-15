<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Auth/Register', [
            'cities' => City::where('type', 'register')->get() ?? [trans('alriyadh')],
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'storeName' => 'required|string|max:255',
            'phone' => 'required|numeric||digits:10',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
            'accepted' => 'accepted',
            'password' => ['required', 'confirmed', Rules\Password::min(6)],

        ],
            [
                'accepted.accepted' => trans('please_accept_terms_and_conditions'),
            ]);

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

        $store = $user->store()->create([
            'name' => $request->storeName,
            'website' => $request->website,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'base_fee' => $base_fee,
            'exchange_fee' => $exchange_fee,
            'refrigerated_transport_fee' => $refrigerated_transport_fee,
            'extra_fees' => $return_fee,
            'type' => $request->type ?? 'shop'
        ]);

        $store->bankAccount()->create([
            'bank_name' => $request->bankName,
            'owner_name' => $request->ownerName,
            'rib' => $request->rib,
        ]);

        event(new Registered($user));

        if ($request->fromDashboard){
            redirect()->route('stores/edit', $store);
        }else {
            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
        }



    }
}
