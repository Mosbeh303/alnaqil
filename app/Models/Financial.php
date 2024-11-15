<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Integrations\Jtexpress\JtexpressHelper;
use Illuminate\Support\Facades\Log;
use DB;


class Financial extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::created(function($financial)
        {
            try{
                $shipment = $financial->shipment;
                if ($shipment->city !== 'الرياض' && $shipment->city !== 'Riyadh' && $shipment->city !== 'الدرعية' && $shipment->city !== 'Ad Diriyah'){
                    $jt = json_decode(JtexpressHelper::createOrder($shipment, $financial->cod));
                    if ($jt->code == 1){
                        $shipment->jtexpress()->create([
                            'order_number' => $jt->data->orderNumber ?? null,
                            'bill_code' => $jt->data->billCode ?? null ,
                            'sum_freight' => $jt->data->sumFreight ?? null,
                            'txlogistic_id' => $jt->data->txlogisticId ?? null,
                            'sorting_code' => $jt->data->sortingCode ?? null,
                            'last_center_name' => $jt->data->lastCenterName ?? null,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error($e);
            }

            try{

                $store = $financial->shipment->store;
                $additional_fees = 0 ;
                $extra_weight = $financial->shipment->weight - $store->base_weight;
                if ($extra_weight > 0){
                  $additional_fees =   ceil($extra_weight) * $store->addtional_fee_per_kg;
                }

                $financial->update([
                    'base_weight' => $store->base_weight,
                    'addtional_fee_per_kg' => $store->addtional_fee_per_kg,
                    'addtional_fees_extra_weight' => $additional_fees
                ]);

            } catch (\Exception $e) {
                Log::error($e);
            }
        });
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }


}
