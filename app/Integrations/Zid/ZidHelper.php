<?php

namespace App\Integrations\Zid;

use App\Models\Shipment ;
use Illuminate\Support\Facades\Log;


class ZidHelper{


    public static function updateOrderStatus(Shipment $shipment){

        try{

            $curl = curl_init();

            $shipmentStatus =  [
                '10' => "ready",
                '20' => "indelivery",
                '35' => "indelivery",
                '65' => "indelivery",
                '100' => "delivered",
                '-100' => "cancelled",
                '-1' => "cancelled"
            ];

            $data = [
                "order_status" => $shipmentStatus[$shipment->status],
                // "inventory_address_id" => "string",
                "waybill_url" => env('APP_URL') .  $shipment->number . "/policy",
                "tracking_number" => $shipment->number,
                "tracking_url" => env('APP_URL') . "tracking?number=" . $shipment->number
            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.zid.sa/v1/managers/store/orders/$shipment->zid_order_id/change-order-status",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer  " . $shipment->store->zid->authorization,
                    "Content-Type: application/json",
                    "X-MANAGER-TOKEN: " . $shipment->store->zid->access_token
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            Log::info('response: ' . $response);

            curl_close($curl);

            if ($err || (json_decode($response)->status ?? 'error') == 'error') {
                Log::info($err);
                return false;
            } else {
                Log::info('res:' . $response);
                return true;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }


        // return $response ;


    }

}
