<?php

namespace App\Jobs;

use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class CreateParcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $shipment;

    protected static $entities = [
        '10' => 9,
        '20' => 9,
        '35' => 4,
        '65' => 10,
        '100' => 10,
        '-100' => 9,
        '-1' => 9

    ];

    protected static $statuses = [
        '10' => 1,
        '20' => 2,
        '35' => 7,
        '65' => 8,
        '100' => 9,
        '-100' => 10,
        '-1' => 13

    ];

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [

            "ParcelDetails" => [
                 "WaybillNumber" => $this->shipment->number,
                 "ParcelCreationDate" => strtotime($this->shipment->created_at),
                 "ParcelType" =>  2,
                 "ParcelStatus" => 1,
                 "ParcelWeight" => floatval($this->shipment->weight),
                 "numberOfPieces" => intval($this->shipment->number_of_pieces),
                 "originCountry" => 100,
                 "originCity" => 100625,
                 "destinationCountry" => 100,
                 "destinationCity" => 100625,
             ],

             "ShipperDetails" => [
                 "shipperContactNumber" => intval(substr_replace($this->shipment->store->user->phone,"966",0,1)),
                 "shipperName" => $this->shipment->store->name,
                 "shipperAddress" => $this->shipment->store->neighborhood .  ',' . $this->shipment->store->city,
             ],

             "consigneeDetails" => [
                 "consigneeContactNumber" =>  intval(substr_replace($this->shipment->receiver_phone ,"966",0,1)),
                 "consigneeName" => $this->shipment->receiver_name,
                 "consigneeAddress" => $this->shipment->neighborhood . ',' . $this->shipment->street . ',' . $this->shipment->city,
             ],

             "FinancialDetails" => [
                 "paymentType" => $this->shipment->financial->cod > 0 ? 2 : 1 ,
                 "shippingFees" => calculateFees($this->shipment->financial),
                 "postpaidAmount" => floatval($this->shipment->financial->cod) ,
             ],

         ];

        try {

            $response = Http::withHeaders([
                'X-Api-Key' => env('TOROOD_API_KEY'),
                'X-Correlation-ID' => uniqid()
            ])->withBody(
                json_encode($data), 'application/json'
            )->post(env('TOROOD_SRV_URL') . 'parcel/add');

            //Log::info($response);

        } catch (\Exception $e) {
           // Log::info($e->getMessage());
        }


    }
}
