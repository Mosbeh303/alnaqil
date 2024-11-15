<?php

namespace App\Http\Controllers;
use Salla\OAuth2\Client\Provider\Salla;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\SendAuthenticationDetails;
use App\Models\SallaOauthToken;


class SallaController extends Controller
{
    public function callback(Request $request){
        $provider = new Salla([
            'clientId'     => env('SALLA_CLIENT_ID') , // The client ID assigned to you by Salla
            'clientSecret' => env('SALLA_CLIENT_SECRET') , // The client password assigned to you by Salla
            'redirectUri'  => env('SALLA_CALLBACK_URL') , // the url for current page in your service
        ]);


        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $request->code
            ]);


            /** @var \Salla\OAuth2\Client\Provider\SallaUser $user */
            $storeDetails = $provider->getResourceOwner($token);

            $userExists = User::where('email', $storeDetails->getEmail())->exists();

            $data = [
                'username' => Str::random(),
                'password' => Str::random()
            ];


            $user = User::query()->firstOrCreate([
                'email' => $storeDetails->getEmail(),
            ], [
                'name'     => $storeDetails->getName(),
                'password' => Hash::make($data['password']),
                'username' => $data['username'],
                'phone' => $storeDetails->getMobile(),
            ]);

            if(!$userExists){
                $base_fee = floatval(Setting::where('option', 'base_fee')->value('value'));
                $exchange_fee = floatval(Setting::where('option', 'exchange_fee')->value('value'));
                $refrigerated_transport_fee = floatval(Setting::where('option', 'refrigerated_transport_fee')->value('value'));
                $return_fee = floatval(Setting::where('option', 'return_fee')->value('value'));
                $store = $user->store()->create([
                    'name' => $storeDetails->getStoreName(),
                    'base_fee' =>  $base_fee,
                    'exchange_fee' =>  $exchange_fee,
                    'refrigerated_transport_fee' =>  $refrigerated_transport_fee,
                    'extra_fees' =>  $return_fee,
                ]);
                event(new Registered($user));

                Auth::login($user);
                session('sallaIntegration', true);

                $user->notify(new SendAuthenticationDetails($data));
            } else {
                session('sallaUserName', $storeDetails->getStoreOwnerName());
            }

            $user->store->token()->delete();

            SallaOauthToken::where('merchant', $storeDetails->getStoreID())->delete();

            $user->store->token()->create([
                'access_token'  => $token->getToken(),
                'expires_in'    => $token->getExpires(),
                'refresh_token' => $token->getRefreshToken(),
                'merchant' => $storeDetails->getStoreID(),
            ]);

            return  redirect()->route('login');

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token or merchant details.
            // show a error message to the merchant with good UI
            exit($e->getMessage());
        }
    }
}
