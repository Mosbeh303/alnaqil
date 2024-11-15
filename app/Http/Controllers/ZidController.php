<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zid;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Log;

class ZidController extends Controller
{
    public function zidRedirect()
    {
        $queries = http_build_query([
            'client_id' => '1800',
            'redirect_uri' => 'https://alnaqil.sa/zid-call-back',
            'response_type' => 'code'
        ]);
        return redirect('https://oauth.zid.sa/oauth/authorize?' . $queries);
    }

    public function zidCallBack(Request $request)
    {
        if (Auth::user()->zid) {
            Auth::user()->zid->delete();
        }

        $response = Http::post('https://oauth.zid.sa' . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 1800,
            'client_secret' => '01CNAYFU0e8lSbEBO9Pf1EM24X83HxebFhQkxNCp',
            'redirect_uri' => 'https://alnaqil.sa/zid-call-back',
            'code' => $request->code, // grant code
        ]);

        Log::info($response);

        $response = $response->json();


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.zid.sa/v1/managers/account/profile",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept-Language: ar",
                "Authorization: Bearer " . $response['authorization'],
                "Content-Type: application/json",
                "User-Agent: ",
                "X-MANAGER-TOKEN: " . $response['access_token'],
            ],
        ]);

        $res = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = json_decode($res, true);

        Zid::where('zid_store_id', $res['user']['store']['id'])->delete();

        $zid = new Zid();
        $zid->access_token = $response['access_token'];
        $zid->refresh_token = $response['refresh_token'];
        $zid->authorization = $response['authorization'];
        $zid->expires_in = $response['expires_in'];
        $zid->token_type = $response['token_type'];
        $zid->user_id = Auth::user()->id;
        $zid->store_id = Auth::user()->store->id;
        $zid->zid_store_id = $res['user']['store']['id'];
        $zid->save();

        if ($err) {
            return redirect()->route('dashboard')->with('error', trans('linking_failed'));
        } else {
            return redirect()->route('dashboard')->with('success', trans('Z_platform_linked_successfully'));
        }


        //return $response;
    }

//     public function addDeliveryOpt(Zid $zid)
//     {

//         $curl = curl_init();

//         $fields = [
//             "cities" => [1],
//             "name" => Env::get('APP_NAME'),
//             "cost" => $zid->store->base_fee,
//             "cod_enabled" => $zid->store->cod_active,
//             "cod_fee" => $zid->store->cod_fee,
//             "delivery_estimated_time_ar" => 0,
//             "delivery_estimated_time_en" => 0,
//             "icon_url" => "https://media.zid.store/apps/080c2e70-e8a0-4250-83fb-902fb8de7162.png"
//         ];


//         curl_setopt_array($curl, [
//             CURLOPT_URL => "https://api.zid.sa/v1/managers/store/delivery-options/add",
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => "",
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => "POST",
//             CURLOPT_POSTFIELDS => json_encode($fields),
//             CURLOPT_HTTPHEADER => [
//                 "Authorization: Bearer " . $zid->authorization,
//                 "Content-Type: application/json",
//                 "X-MANAGER-TOKEN: " . $zid->access_token
//             ],
//         ]);

//         $response = curl_exec($curl);
//         $err = curl_error($curl);

//         curl_close($curl);

//         if ($err) {
//             $zid_redirect_error = $err;
//             return redirect()->route('dashboard')->with('error', trans('linking_failed'));
//         } else {
//             //$zid_redirect_error = null;

//             $response = json_decode($response, true);

//             return $this->createWebhook($response, $zid);
//         }


//     }

//     public function createWebhook($response, $zid)
//     {

//         $curl = curl_init();
//         $conditions = [
//             "status" => "ready",
//             "delivery_option_id" => $response['delivery_option']['id'],
//         ];

//         $fields = [
//             'event' => 'order.status.update',
//             'target_url' => 'https://dev.alnaqil.sa/zid-webhook',
//             'original_id' => (string) Auth::user()->id,
//             'subscriber' =>  Env::get('APP_NAME'),
//             'conditions' => $conditions,
//         ];

//         curl_setopt_array($curl, [
//             CURLOPT_URL => "https://api.zid.sa/v1/managers/webhooks",
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => "",
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => "POST",
//             CURLOPT_POSTFIELDS => json_encode($fields),
//             CURLOPT_HTTPHEADER => [
//                 "Authorization: Bearer " . $zid->authorization,
//                 "Content-Type: application/json",
//                 "X-MANAGER-TOKEN: " . $zid->access_token
//             ],
//         ]);

//         $response = curl_exec($curl);
//         $err = curl_error($curl);

//         $response = json_decode($response, true);


//         curl_close($curl);

//         if ($err) {
//             return redirect()->route('dashboard')->with('error', trans('linking_failed'));
//         } else {
//             return redirect()->route('dashboard')->with('success', trans('Z_platform_linked_successfully'));
//         }

//     }
}
