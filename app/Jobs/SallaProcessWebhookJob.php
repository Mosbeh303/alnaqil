<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob ;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Models\Store;
use App\Models\Shipment;
use App\Models\SallaOauthToken;
use App\Integrations\Torood\ParcelHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Notifications\SendAuthenticationDetails;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
// use Salla\OAuth2\Client\Provider\Salla;

use Throwable;




class SallaProcessWebhookJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $data;
    public $event;
    public $merchant;


    public function __construct($data, $event, $merchant)
    {
        $this->onConnection('sync');
        $this->data = $data;
        $this->event = $event;
        $this->merchant = $merchant;
    }


    public function handle()
    {

            //$data = json_decode($this->webhookCall);

            Log::info($this->data);

            if ($this->event === "app.store.authorize")
                $this->storeAuthorize($this->data);

            if ($this->event === "shipment.creating")
                $this->createShipment($this->data);

            if  ($this->event === "shipment.cancelled")
                $this->cancelShipment($this->data);

    }

    private function storeAuthorize($data){
        try {

            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $data['access_token'],
            ])->get('https://api.salla.dev/admin/v2/oauth2/user/info');


            $response = (object) $res;

            Log::info($response);

            $userExists = User::where('email', $response['data']['email'])->exists();



            $credentials = [
                'username' => Str::random(),
                'password' => Str::random()
            ];


            $user = User::query()->firstOrCreate([
                'email' => $response['data']['email'],
            ], [
                'name'     => $response['data']['name'],
                'password' => Hash::make($credentials['password']),
                'username' => $credentials['username'],
                'phone' => $response['data']['mobile'],
            ]);

           // Log::info($user);

           // Log::info('hhaa');

            if(!$userExists){
                $base_fee = floatval(Setting::where('option', 'base_fee')->value('value'));
                $exchange_fee = floatval(Setting::where('option', 'exchange_fee')->value('value'));
                $refrigerated_transport_fee = floatval(Setting::where('option', 'refrigerated_transport_fee')->value('value'));
                $return_fee = floatval(Setting::where('option', 'return_fee')->value('value'));
                $user->store()->create([
                    'name' => $response['data']['name'],
                    'base_fee' =>  $base_fee,
                    'exchange_fee' =>  $exchange_fee,
                    'refrigerated_transport_fee' =>  $refrigerated_transport_fee,
                    'extra_fees' =>  $return_fee,
                ]);
                event(new Registered($user));

                $user->notify(new SendAuthenticationDetails($credentials));
            }

            //$user->store->token()->delete();

            SallaOauthToken::where('merchant', $this->merchant)->delete();

           $token =  $user->store->token()->create([
                'access_token'  => $data['access_token'],
                'expires_in'    => $data['expires'],
                'refresh_token' => $data['refresh_token'],
                'merchant' => $this->merchant,
            ]);

        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    private function createShipment()
    {


        $number = str_pad(mt_rand(1,999999999),10,'1',STR_PAD_LEFT); //! Should review

        $merchant = $this->merchant ;

        $store = Store::whereHas('token', function ($query) use ($merchant) {
            $query->where('merchant', $merchant);
        })->first();

        $data = (object) $this->data;

        if ($store){

            try {
                \DB::beginTransaction();

                $neighborhood = ' ';
                if ($data->type === 'return' ){
                    if ($data->ship_from['block'] !=  null) {
                       $neighborhood =  $data->ship_from['block'];
                    }
                } else {
                    if ($data->ship_to['block'] !=  null) {
                        $neighborhood =  $data->ship_to['block'];
                     }
                }

                $details = "";

                //$token = SallaOauthToken::where('merchant', $merchant)->latest('id')->first();

                // foreach( $data->packages as $package){
                //     $response = Http::withHeaders([
                //         'Content-Type' => 'application/json',
                //         'Authorization' => "Bearer $token->access_token",
                //     ])->withBody(
                //         json_encode($data), 'application/json'
                //     )->get("https://api.salla.dev/admin/v2/products/" . $package['item_id'] );

                //     Log::info($response);
                //     $details = $details . ', ' . $package['quantity'] . ' ' . $package['name'] ;
                // }

                $freeBalance = calculateStoreWallet($store)['freeBalance'];

                $shipment = $store->shipments()->create([
                    'number' => $number,
                    'status' => 10,
                    'receiver_name' => $data->type === 'return' ? $data->ship_from['name'] : $data->ship_to['name'] ,
                    'city' => $data->type === 'return' ? $data->ship_from['city'] : $data->ship_to['city'] ,
                    'street' => $data->type === 'return' ? $data->ship_from['street_number'] : $data->ship_to['street_number'] ,
                    'neighborhood' => $neighborhood,
                    'receiver_phone' => $data->type === 'return' ? $data->ship_from['phone'] : $data->ship_to['phone'],
                    'details' => $details,
                    'salla_order_id' => $data->id,
                    'source' => 'salla',
                    'return_back' => $data->type === "return" ? 1 : 0 ,
                    'weight' => $data->total_weight['value'],
                ]);

                $cod = $data->payment_method == 'cod' ? $data->total['amount'] : 0;

                $city = \App\Models\City::where('name', $data->ship_to['city'])->orWhere('name_en', $data->ship_to['city'])->first();

                $base_fee = $store->base_fee3;
                if ($city) {
                    switch ($city->fee_range) {
                        case '1':
                            $base_fee = $store->base_fee;
                            break;
                        case '2':
                            $base_fee = $store->base_fee2;
                            break;
                    }
                }


                $cod_fee = 0 ;
                if ($store->cod_active && $cod > 0){
                    if ($cod > floatval($store->fixed_amount_cod_fee) && $store->fixed_amount_cod_fee !== null)
                        $cod_fee = $cod * $store->cod_fee_percent/100 ;
                    else
                        $cod_fee = floatval($store->cod_fee) ;
                }



                $extra_fees = 0;
                if ($data->type === "return"){
                    $extra_fees = floatval($store->return_fee) ;
                }
                $totalfees = $base_fee + $cod_fee + $extra_fees;


                if(!$store->postpaid_active && $totalfees <= $freeBalance   )
                    $wallet = 1 ;
                else
                    $wallet = 0    ;

                $financial = $shipment->financial()->create([
                    'cod' => $cod ,
                    'base_fee'=> floatval($base_fee),
                    'cod_fee' => $cod_fee ,
                    'extra_fees' => $extra_fees,
                    'tax' => null,
                    'wallet' => $wallet
                ]);

                $track = $shipment->shipmentTracks()->create([
                    'user_id' => $store->user->id,
                    'status_after_action' => $shipment->status    ,
                    'action' => 'create'  ,
                ]);

                if (!$store->postpaid_active && $totalfees > $freeBalance && $totalfees > -getStoreDues($store)){
                    \DB::rollBack();

                } else {
                    \DB::commit();

                    $response = json_decode($this->updateShipmentDetails($number, $totalfees));

                    //Log::info($response->success);
                    if ($response->success){
                        ParcelHelper::create($shipment);

                    } else{
                        $shipment->delete();
                        //$financial->delete();
                        //$track->delete();
                    }
                }


            }catch (Throwable $e) {
                \DB::rollback();
                Log::error($e);
            }

        }
    }

    private function updateShipmentDetails($number, $fees){
        $data = [
            "tracking_link" =>  env('APP_URL') . "tracking?number=" . $number,
            "shipment_number" => $number,
            "pdf_label" => env('APP_URL') .  $number . "/policy",
            'cost' =>  $fees
        ];

        $token = SallaOauthToken::where('merchant', $this->merchant)->latest('id')->first();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $token->access_token",
        ])->withBody(
            json_encode($data), 'application/json'
        )->put("https://api.salla.dev/admin/v2/shipments/" . $this->data['id'] );

       Log::info($response);

        return $response;
    }

    private function cancelShipment(){


        $shipment = Shipment::where('salla_order_id', $this->data['id'])->where('status',10)->first();


        if ($shipment){
            $status_before_action = $shipment->status;

            $update = $shipment->update([
                'status' => -1,
            ]);

            if ($update){
                $fees = calculateFees($shipment->financial) ;
                $tax = $fees - $fees/((float)getSettingValue('vat') * 0.01 + 1) ;

                $shipment->financial()->update([
                    'tax'            => $tax ,
                ]);

                $shipment->shipmentTracks()->create([
                    'user_id' => $shipment->store->user->id,
                    'status_after_action' => $shipment->status    ,
                    'status_before_action' => $status_before_action   ,
                    'action' => 'update_status'  ,
                ]);

                ParcelHelper::tracking($shipment);

                Log::info("shipment cancelled successfully!");
            }
        }



    }

}
