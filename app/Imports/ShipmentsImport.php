<?php

namespace App\Imports;

use App\Models\Shipment;
use DB;
use App\Models\Store;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Integrations\Torood\ParcelHelper;

class ShipmentsImport implements OnEachRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public $j = 0;
    public $count = 0 ;

    public function onRow(Row $row)
    {


        $this->count++ ;

        try {


            $number = str_pad(mt_rand(1,999999999),10,'1',STR_PAD_LEFT); //! Should review

            if (auth()->user()->type == "client")
                $store = auth()->user()->store ;
            else
                $store = Store::find($row[9]);


            if ($store && $row[8] <= 2){

                DB::beginTransaction();

                $freeBalance = calculateStoreWallet($store)['freeBalance'];

                $phone = str_replace("966", "0", $row[4]);

                $shipment = $store->shipments()->create([
                    'number' => $number,
                    'status' => 10,
                    'receiver_name' => $row[0],
                    'city' => $row[1] ,
                    'neighborhood' => $row[2],
                    'street' => $row[3],
                    'receiver_phone' => $phone,
                    'details' => $row[6],
                    'weight'  => $row[7],
                    'number_of_pieces' => $row[8],
                ]);

                $cod = $row[5]  ? $row[5] : 0 ;
                $cod_fee = 0 ;
                if ($store->cod_active && $cod > 0){
                    if ($cod > $store->fixed_amount_cod_fee && $store->fixed_amount_cod_fee !== null)
                        $cod_fee = $cod * $store->cod_fee_percent/100 ;
                    else
                        $cod_fee = $store->cod_fee ;
                }

                $city = \App\Models\City::where('name', $row[1])->first();
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

                $totalfees = $base_fee + $cod_fee ;

                if(!$store->postpaid_active && $totalfees <= $freeBalance  )
                    $wallet = 1 ;
                else
                    $wallet = 0    ;

                $financial = $shipment->financial()->create([
                    'cod' => $cod ,
                    'base_fee'=> $base_fee ,
                    'cod_fee' => $cod_fee ,
                    'tax' => null,
                    'wallet' => $wallet
                ]);

                $track = $shipment->shipmentTracks()->create([
                    'user_id' => auth()->user()->id,
                    'status_after_action' => $shipment->status    ,
                    'action' => 'create'  ,

                ]);

                if (!$store->postpaid_active && $totalfees > $freeBalance && $totalfees > -getStoreDues($store)){
                    DB::rollBack();
                } else {
                    DB::commit();
                    $toroodResponse = ParcelHelper::create($shipment);
                    $this->j++;
                }

            }
            // DB::commit();



        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            //return Redirect::back()->with('error', $e->getMessage());
        }


    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8'
        ];
    }
}
