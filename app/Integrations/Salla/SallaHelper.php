<?php

namespace App\Integrations\Salla;
use Request;
use App\Models\Shipment ;
use App\Models\Store ;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Salla\OAuth2\Client\Provider\Salla;
// use Salla\OAuth2\Client\Provider\SallaUser;

use App\Models\SallaOauthToken;
use App\Integrations\Torood\ParcelHelper;

use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Notifications\SendAuthenticationDetails;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Support\Str;
// use Salla\OAuth2\Client\Provider\Salla;

use Throwable;



class SallaHelper{

    public $token;
    protected $provider;

    public function __construct()
    {
        $this->provider = new Salla([
            'clientId'     => env('SALLA_CLIENT_ID') , // The client ID assigned to you by Salla
            'clientSecret' => env('SALLA_CLIENT_SECRET') , // The client password assigned to you by Salla
            'redirectUri'  => env('SALLA_CALLBACK_URL') , // the url for current page in your service
        ]);
    }

    public function forStore(Store $store)
    {
        $this->token = $store->token;

        return $this;
    }

    /**
     * @return Salla
     */
    public function getProvider(): Salla
    {
        return $this->provider;
    }

     /**
     * Get A new access token via refresh token.
     *
     * @return \League\OAuth2\Client\Token\AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getNewAccessToken()
    {
        if ($this->token->hasExpired()) {
            return new AccessToken($this->token->toArray());
        }

        // let's request a new access token via refresh token.
        $token = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->token->refresh_token
        ]);

        // lets update user tokens
        $this->token->update([
            'access_token'  => $token->getToken(),
            'expires_in'    => $token->getExpires(),
            'refresh_token' => $token->getRefreshToken()
        ]);

        return $token;
    }

    public function request(string $method, string $url, array $options = [])
    {
        // you need always to check the token before made a request
        // If the token expired, lets request a new one and save it to the database
        $this->getNewAccessToken();

        return $this->provider->fetchResource($method, $url, $this->token->access_token, $options);
    }

    public static function updateShipmentStatus(Shipment $shipment){

        try {
            $salla_order_id = $shipment->salla_order_id ;
            $token = $shipment->store->token;

            $statuses = [
                "10" => "created",
                "20" => "in_progress" ,
                "35" => "in_progress",
                "65" => "in_progress",
                "100" => "delivered",
                "-100" => "returned",
                "-1" => "cancelled"
            ] ;

            // $shipmentStatus = [
            //     '10' => "تم انشاء الشحنة",
            //     '20' => "تم استلام الشحنة",
            //     '35' => "جاري التجهيز",
            //     '65' => "تم التجهيز, بانتظار التوصيل",
            //     '100' => "تم التوصيل",
            //     '-100' => "مرتجعه",
            //     '-1' => "ملغية"
            // ];

            $data = [
                "shipment_number" => $shipment->number,
                "status" => $statuses[$shipment->status],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token->access_token",
            ])->withBody(
                json_encode($data), 'application/json'
            )->put("https://api.salla.dev/admin/v2/shipments/" . $salla_order_id ) ;

            Log::info($response);

        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }


    }




}
