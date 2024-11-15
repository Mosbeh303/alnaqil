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

class UpdateParcelFinancial implements ShouldQueue
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


        $data = [
            "WaybillNumber" => $this->shipment->number,
            "paymentType" => $this->shipment->financial->cod > 0 ? 2 : 1 ,
            "shippingFees" => calculateFees($this->shipment->financial),
            "postpaidAmount" => floatval($this->shipment->financial->cod) ,
            "TotalValue"    =>  floatval($this->shipment->financial->cod) ,
            "VAT" => floatval(number_format((float)$vat, 2, '.', ''))
        ];

        try {

            $response = Http::withHeaders([
                'X-Api-Key' => env('TOROOD_API_KEY'),
            ])->withBody(
                json_encode($data), 'application/json'
            )->post(env('TOROOD_SRV_URL') . 'parcel/updateFinancialDetails');

           // Log::info($response);

        } catch (\Exception $e) {
           // Log::info($e->getMessage());
        }



    }
}
