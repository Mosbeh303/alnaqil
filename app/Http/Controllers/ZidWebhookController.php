<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZidWebhookRequest;
use App\Integrations\Torood\ParcelHelper;
use App\Integrations\Zid\ZidHelper;
use App\Models\PickupAddress;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Shipment;

use Throwable;

class ZidWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info($request);

        $data =  (object) $request;

        if($data->order_status['code'] == 'cancelled')
            $this->cancelShipment($data);

        if($data->order_status['code'] == 'ready')
            $this->createShipment($data);

        //return response('🎉');
    }

    private function createShipment($data)
    {
        $number = str_pad(mt_rand(1,999999999),10,'1',STR_PAD_LEFT); //! Should review

        $store = Store::whereHas('zid', function ($query) use ($data) {
            $query->where('zid_store_id', $data->store_id);
        })->first();

        //Log::info($data->order_status['code']);
        //Log::info($store);

        if ($store){

            $cod = $data['payment']['method']['type'] == "zid_cod"  ? $data->payment['invoice'][3]['value'] : 0 ;
            $fees = $store->base_fee ;

            try {
                \DB::beginTransaction();
                $freeBalance = calculateStoreWallet($store)['freeBalance'];

                $shipment = $store->shipments()->create([
                    'number' => $number,
                    'status' => 10,
                    'receiver_name' => $data->has_different_consignee ? $data->consignee['name'] : $data->customer['name'],
                    'city' => $data->shipping['address']['city']['name'] ,
                    'street' => $data->shipping['address']['street'] ,
                    'neighborhood' => $data->shipping['address']['district'],
                    'receiver_phone' => $data->has_different_consignee ? $data->consignee['mobile'] : $data->customer['mobile'],
                    'details' => $data->products[0]['name'],
                    'zid_order_id' => $data->id,
                    'source' => 'zid',
                    'number_of_pieces' => $data->packages_count ,
                    // 'return_back' => $type === "return" ? 1 : 0 ,
                ]);

                $pickup_address = $store->pickupAddresses()->where('full_address',  $data->inventory_address['street'])->latest()->first(); //$data->inventory_address['full_address'] ??

                if (!$pickup_address)
                    $pickup_address = $store->pickupAddresses()->create([
                        'name' => $data->inventory_address['name'],
                        'city' => $data->inventory_address['city']['ar_name'],
                        'neighborhood' => $data->inventory_address['district'],
                        'national_address' => null,
                        'full_address' =>  $data->inventory_address['street']

                    ]);

                $shipment->pickup_address_id = $pickup_address->id ;
                $shipment->save();

                $city = \App\Models\City::where('name', $data->shipping['address']['city']['name'])->orWhere('name_en', $data->shipping['address']['city']['name'])->first();

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

                $totalfees = $base_fee + $cod_fee;

                if(!$store->postpaid_active && $totalfees <= $freeBalance   )
                    $wallet = 1 ;
                else
                    $wallet = 0    ;

                $financial = $shipment->financial()->create([
                    'cod' => $cod ,
                    'base_fee'=> floatval($base_fee),
                    'cod_fee' => $cod_fee ,
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

                    $response = ZidHelper::updateOrderStatus($shipment);

                    Log::info('res:' . $response);

                    if ($response){
                        ParcelHelper::create($shipment);
                    } else{
                        $shipment->delete();
                        $financial->delete();
                        $track->delete();
                    }
                }


            }catch (Throwable $e) {
                Log::error($e);
                \DB::rollback();
            }

        }
    }

    private function cancelShipment($data){

        $shipment = Shipment::where('zid_order_id', $data->id)->where('status',10)->first();


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

                $track = $shipment->shipmentTracks()->create([
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
