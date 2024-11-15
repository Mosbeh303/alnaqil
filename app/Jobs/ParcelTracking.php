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

class ParcelTracking implements ShouldQueue
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
        $fees = calculateFees($this->shipment->financial) ;
        //vat
        $vat = $fees - $fees/((float)getSettingValue('vat') * 0.01 + 1) ;

        $paymentMethod = null;
        $pickupDate = null ;

        if ($this->shipment->financial->cod > 0 && $this->shipment->status == 100){
            $paymentMethod = $this->shipment->financial->payment_mehtod == 'cash' ? 1 : 2;
            $data = [
                "WaybillNumber" => $this->shipment->number,
                "ParcelStatus" => self::$statuses[$this->shipment->status],
                "ParcelStatusDate" => strtotime($this->shipment->updated_at),
                "ParcelTrackingEntity" => self::$entities[$this->shipment->status],

                "AdditionalInfo" => [
                    "paymentMethod" => $paymentMethod,
                ]
            ];
        }elseif ($this->shipment->status == 100){
            $data = [
                "WaybillNumber" => $this->shipment->number,
                "ParcelStatus" => self::$statuses[$this->shipment->status],
                "ParcelStatusDate" => strtotime($this->shipment->updated_at),
                "ParcelTrackingEntity" => self::$entities[$this->shipment->status],

                "AdditionalInfo" => [
                    "FirstDeliveryAttemptDate" =>  strtotime($this->shipment->updated_at),
                ]
            ];
         }


        elseif ($this->shipment->status == 20){
            $pickupDate  = strtotime($this->shipment->updated_at) ;

            $data = [
                "WaybillNumber" => $this->shipment->number,
                "ParcelStatus" => self::$statuses[$this->shipment->status],
                "ParcelStatusDate" => strtotime($this->shipment->updated_at),
                "ParcelTrackingEntity" => self::$entities[$this->shipment->status],
                "AdditionalInfo" => [
                    "pickupDate" => $pickupDate ,
                    "VAT" => floatval(number_format((float)$vat, 2, '.', '')),
                    "TotalValueAmount" => floatval($this->shipment->financial->cod) ,
                ]
            ];
        } else
            $data = [
                "WaybillNumber" => $this->shipment->number,
                "ParcelStatus" => self::$statuses[$this->shipment->status],
                "ParcelStatusDate" => strtotime($this->shipment->updated_at),
                "ParcelTrackingEntity" => self::$entities[$this->shipment->status],

            ];



        try {

            $response = Http::withHeaders([
                'X-Api-Key' => env('TOROOD_API_KEY'),
                'X-Correlation-ID' => uniqid()
            ])->withBody(
                json_encode($data), 'application/json'
            )->post(env('TOROOD_SRV_URL') . 'parcel/tracking');

           // Log::info($response);

        } catch (\Exception $e) {
           // Log::info($e->getMessage());
        }
    }
}
