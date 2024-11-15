<?php

namespace App\Http\Controllers;

use App\Integrations\Jtexpress\JtexpressHelper;
use App\Integrations\Salla\SallaHelper;
use App\Integrations\Zid\ZidHelper;
use App\Models\Jtexpress;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Log;
use Throwable;

class JtexpressController extends Controller
{
    public function print($billCode){
        $jt = json_decode(JtexpressHelper::printOrder($billCode));
        if ($jt->code == 1)
            return redirect()->away($jt->data);
        else
            Log::error(json_encode($jt));
    }

    public function create(Shipment $shipment){
        $jt = json_decode(JtexpressHelper::createOrder($shipment, $shipment->financial->cod));
        if ($jt->code == 1){
            $shipment->jtexpress()->create([
                'order_number' => $jt->data->orderNumber ?? null,
                'bill_code' => $jt->data->billCode ?? null ,
                'sum_freight' => $jt->data->sumFreight ?? null,
                'txlogistic_id' => $jt->data->txlogisticId ?? null,
                'sorting_code' => $jt->data->sortingCode ?? null,
                'last_center_name' => $jt->data->lastCenterName ?? null,
            ]);
            return back()->with('success', trans('order_created_successfully'));
        }
        return back()->with('error', trans('somthing_wrong'));
    }

    public function webhook(Request $request){
        //Log::info($request);

        $statuses = [
            "Pickup scan" => 20,
            "Station sending/DC sending" => 35 ,
            "Station arrival" => 35,
            "DC arrival" => 35,
            "Sign scan" => 100,
            "Delivery scan" => 65,
           // "Abnormal parcel scan" => "35",
            "Returned parcel scan" => 65,


            //"Change Address scan" => "35",
            "Returned signed" => 65,
            // "International warehousing" => "35",
            // "International Air Arrival" => "35",
            // "International Air Shipment" => "35",

            // "Customs declaration scan" => "35",
            // "Customs clearance scan" => "35",
        ] ;

        try{
           // Log::info('I am here');

            //Log::info($request['bizContent']->billCode);
            $biz = json_decode(json_encode($request['bizContent']));

            $biz = json_decode($biz);

           // Log::info($biz->billCode);

            $details =  $biz->details;

            Log::info($biz->details);

            $lastScanType = $details[0]->scanType;

            Log::info($lastScanType);

            $jt =  Jtexpress::where('bill_code', $biz->billCode)->latest()->first();

            if ($jt){
                $shipment = $jt->shipment;

                if ($lastScanType !=  "Abnormal parcel scan"){
                    $shipment->status = $statuses[$lastScanType];
                }

                if ($lastScanType == "Returned signed"){
                    $shipment->failed = 3 ;
                }

                $shipment->operator_id = null ;

                $shipment->save();

                if ($shipment->salla_order_id)
                    SallaHelper::updateShipmentStatus($shipment);

                if ($shipment->zid_order_id)
                    ZidHelper::updateOrderStatus($shipment);

            }
           // return $request;
        }catch (Throwable $e) {
                Log::error($e);
        }

    }
}
