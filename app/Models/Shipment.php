<?php

namespace App\Models;

use App\Models\Receiver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Integrations\Jtexpress\JtexpressHelper;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendCreatedMessage;
use App\Jobs\SendPushNotification;

use League\CommonMark\Extension\CommonMark\Parser\Inline\EntityParser;

class Shipment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::created(function ($shipment) {

            try {

                if (substr($shipment->receiver_phone, 0, 3) == '966') {
                    $shipment->receiver_phone = 0 . substr($shipment->receiver_phone, 3);
                    $shipment->save();
                }
            } catch (\Exception $e) {
                Log::error($e);
            }

            // try{

            //     $newInvoiceNumber = DB::table('shipments')->max('invoice_number') + 1;
            //     $shipment->update(['invoice_number' => $newInvoiceNumber]);

            // } catch (\Exception $e) {
            //     Log::error($e);
            // }

            try {
                // $receiverPhone = trim($shipment->receiver_phone);
                $receiverPhone = preg_replace("/[^0-9]/", "", $shipment->receiver_phone);
                $existingReceiver = Receiver::where('phone', $receiverPhone)->first();

                if (!$existingReceiver) {
                    $receiver = new Receiver();
                    $receiver->name = $shipment->receiver_name;
                    $receiver->phone = $receiverPhone;
                    $receiver->city = $shipment->city;
                    $receiver->neighborhood = $shipment->neighborhood;
                    $receiver->street = $shipment->street;
                    $receiver->id_number = null;
                    $receiver->door_number = null;
                    $receiver->location_adress = null;
                    $receiver->location_data =  json_encode([
                        'latitude' => null,
                        'longitude' => null,
                    ]);
                    $receiver->national_adress = null;
                    $receiver->door_photo = null;

                    $receiver->save();
                    $shipment->update([
                        'receiver_id' => $receiver->id
                    ]);
                } else {
                    $shipment->update([
                        'receiver_id' => $existingReceiver->id
                    ]);
                }


                SendCreatedMessage::dispatch($shipment);
            } catch (\Exception $e) {
                Log::error($e);
            }

            try {
                $adr = $shipment->store->defaultPickupAddress();
                if ($adr) {
                    $shipment->pickup_address_id = $adr->id;
                    $shipment->save();
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        });

        static::updated(function ($shipment) {

            try {
                if ($shipment->wasChanged('status') && $shipment->status != 10 && $shipment->store->user->whereHas('devicesTokens')) {
                    $shipmentStatuses = [
                        '10' => [
                            'en' => 'The shipment has been created',
                            'ar' => 'تم انشاء الشحنة',
                        ],
                        '20' => [
                            'en' => 'Shipment received',
                            'ar' => 'تم استلام الشحنة',
                        ],
                        '35' => [
                            'en' => 'Processing in progress',
                            'ar' => 'جاري التجهيز',
                        ],
                        '65' => [
                            'en' => 'Prepared, awaiting delivery',
                            'ar' => 'تم التجهيز, بانتظار التوصيل',
                        ],
                        '100' => [
                            'en' => 'Delivered',
                            'ar' => 'تم التوصيل',
                        ],
                        '-100' => [
                            'en' => 'Audit',
                            'ar' => 'مرتجعة',
                        ],
                        '-1' => [
                            'en' => 'Shipment canceled',
                            'ar' => 'تم الغاء الشحنة',
                        ],
                    ];

                    $status = $shipment->status;
                    $enStatusValue = '';
                    $arStatusValue = '';
                    if (isset($shipmentStatuses[$status])) {
                        $statusText = $shipmentStatuses[$status];
                        $enStatusValue = $statusText['en'];
                        $arStatusValue = $statusText['ar'];
                    }

                    //push notification
                    $arBody = 'تغيرت حالة الشحنة إلى ' . $arStatusValue;
                    $enBody = 'Shipment status updated to ' . $enStatusValue;

                    $arTitle = ': تم تعديل حالة الشحنة رقم ' . $shipment->number;
                    $enTitle = 'Updated status of Shipment number: ' . $shipment->number;

                    $title= [
                        'ar' => $arTitle,
                        'en' => $enTitle,
                    ];
                    $body= [
                        'ar' => $arBody,
                        'en' => $enBody,
                    ];
                    //SendPushNotification::dispatch($shipment->store->user, $title, $body);
                }
            } catch (\Throwable $th) {
                Log::error($th);
            }


            try {
                if ($shipment->wasChanged('status') && $shipment->status == -1 && $shipment->jtexpress) {
                    $shipment->jtexpress->delete();
                }
            } catch (\Throwable $th) {
                Log::error($th);
            }


            try {
                if ($shipment->wasChanged('status')) {
                    webhookCall($shipment, $shipment->store->user->webhook ?? null);
                }

                if ($shipment->wasChanged('status') && $shipment->status == -100) {
                    $shipment->financial->update([
                        'base_fee' => $shipment->store->returned_fee,
                        'refrigerated_transport_fee' => 0,
                        'exchange_fee' => 0,
                        'extra_fees' => 0,
                        'cod_fee' => 0,
                    ]);
                }

                if ($shipment->wasChanged('status') && $shipment->status == -1 && ($shipment->jtexpress ?? null)) {
                    Log::info(JtexpressHelper::cancelOrder($shipment->jtexpress));
                }
            } catch (\Exception $e) {
                Log::error($e);
            }

            try {

                if ($shipment->wasChanged('status') && $shipment->status == 100) {

                    $marketer = $shipment->store->marketer;

                    if ($marketer) {

                        if ($marketer->commission_type == 'fixed') {
                            $comissionAmount = $marketer->commission_value;
                        } else {
                            $fees = calculateFees($shipment->financial);
                            $comissionAmount = ($marketer->commission_value / 100) * ($fees / 1.15);
                        }

                        $marketer->commissions()->create([
                            'shipment_id' => $shipment->id,
                            'amount' => $comissionAmount
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        });
    }

    public function jtexpress()
    {
        return $this->hasOne(Jtexpress::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function pickupAddress()
    {
        return $this->belongsTo(PickupAddress::class);
    }

    public function notices()
    {
        return $this->hasMany(ShipmentNotice::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function latestNotice()
    {
        return $this->hasOne(ShipmentNotice::class)->latest();
    }

    public function noticeForClient()
    {
        return $this->latestNotice()->whereHas('user', function ($query) {
            return $query->where('type', 'Operator');
        });
    }

    public function shipmentTracks()
    {
        return $this->hasMany(ShipmentTrack::class);
    }

    public function financial()
    {
        return $this->hasOne(Financial::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function receiver()
    {
        return $this->belongsTo(Receiver::class);
    }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('number', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('street', 'like', '%' . $search . '%')
                    ->orWhere('neighborhood', 'like', '%' . $search . '%')
                    ->orWhere('receiver_phone', 'like', '%' . $search . '%')
                    ->orWhere('warehouse_location', 'like', '%' . $search . '%')
                    ->orWhereHas('store', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when(!isset($filters['status']), function ($query) {
            $query->whereIn('status', [10, 20, 35, 65]);
        });
    }

    public function dailyShipments()
    {
        return $this->hasMany(DailyOperatorShipment::class);
    }

    public function waybillPrints()
    {
        return $this->hasMany(WaybillPrint::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }
}
