<?php

use App\Models\Commission;
use App\Models\Financial;
use App\Models\Marketer;
use App\Models\Setting;
use Spatie\WebhookServer\WebhookCall;
use App\Models\Store;
use App\Models\Voucher;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;


if (!function_exists('sendPushNotification')) {
    function sendPushNotification($shipment)
    {
        try {
            $user = $shipment->store->user;

            if (!$user) {
                throw new Exception('User not found for shipment.');
            }

            $registrationIds = $user->devicesTokens->pluck('value')->toArray();

            if (empty($registrationIds)) {
                throw new Exception('No device tokens found for the user.');
            }

            $data = ['shipment_status' => $shipment->status];

            $notification = [
                'title' => $shipment->number . ' ' . trans('shipment_no') . ':',
                'body' => trans('shipment_status_updated'),
                'sound' => 'default',
            ];

            $message = [
                'registration_ids' => $registrationIds,
                'notification' => $notification,
                'data' => $data,
            ];

            // Set up cURL
            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

            // Set headers
            $serverKey = 'AAAAXUw4q7M:APA91bGX1t-AsfAyWiQVxTVa9rmb2tg9PAUtGzjwhO7J-M6B24FAxxFBx_ofDqZZnL9Rj8CwdKYE8ukLCrdP_OZBfz97SDYFktCJ6Ibh-qe6uUllpnheEwEkW3Xz2M-GgzWakAQ-hj3Y';
            $headers = [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL request
            $response = curl_exec($ch);
            curl_close($ch);

            // Handle response (you may want to log it or perform additional actions)
            \Log::info("FCM Response: $response");

            // Return the response (if needed)
            return $response;
        } catch (Exception $e) {
            // Handle exceptions
            \Log::error("FCM Notification Error: " . $e->getMessage());
            return false;
        }
    }
}



if (! function_exists('calculateFees')) {
    function calculateFees($f ) { // $f means $financial
      if (isset($f->ADP)) {

        return $f->ADP;

      } else {

        $baseFee = isset($f->base_fee) ? (float) $f->base_fee : 0;
        $refrigeratedTransportFee = isset($f->refrigerated_transport_fee) ? (float) $f->refrigerated_transport_fee : 0;
        $extraFees = isset($f->extra_fees) ? (float) $f->extra_fees : 0;
        $exchangeFee = isset($f->exchange_fee) ? (float) $f->exchange_fee : 0;
        $codFee = isset($f->cod_fee) ? (float) $f->cod_fee : 0;
        $addtionalFees = isset($f->addtional_fees_extra_weight) ? (float) $f->addtional_fees_extra_weight : 0;
        return $baseFee + $refrigeratedTransportFee + $extraFees + $exchangeFee + $codFee + $addtionalFees;

      }


    }

}

if (! function_exists('getStoreFees')) {
    function getStoreFees($storeId, $shipmentStatuses = [100, -100], $wallet = 0, $year = null, $month = null ) {

       $fees = calculateFees(Financial::whereHas('shipment', function ($query) use ($storeId , $shipmentStatuses, $year, $month) {
            $query->whereIn('status', $shipmentStatuses)
                ->where('store_id', $storeId)
                ->when($year ?? null, function ($query, $year) {
                    $query->whereYear('updated_at', $year);
                })->when($month ?? null, function ($query, $month) {
                    $query->whereMonth('updated_at', $month);
                });
            })->where('wallet', $wallet)
            ->where('ADP', null)
            ->select(
                DB::raw("SUM(base_fee) as base_fee"),
                DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                DB::raw("SUM(extra_fees) as extra_fees"),
                DB::raw("SUM(exchange_fee) as exchange_fee"),
                DB::raw("SUM(cod_fee) as cod_fee"),
                DB::raw("SUM(addtional_fees_extra_weight) as addtional_fees_extra_weight"),
            )->first()
        ) +  Financial::whereHas('shipment', function ($query) use ($storeId , $shipmentStatuses) {
            $query->whereIn('status', $shipmentStatuses)
                ->where('store_id', $storeId);
            })->where('wallet',  $wallet)
            ->where('ADP', '!=' , null)
            ->sum('ADP');

        return $fees ;

    }

}

if (! function_exists('calculateStoreDues')) {
    function calculateStoreDues($store ) {
        return number_format((float) - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']), 2, '.', '');
    }
}

if (! function_exists('getEmployeeDue')) {
    function getEmployeeDue(Employee $employee ) {

        try{
            $vouchersSum = $employee->vouchers()
            ->where('type', '=', 'operator')
            ->sum('amount');

            $reimboursementSum = $employee->reimbursements()
            ->sum('amount');

            return $vouchersSum -  $reimboursementSum ;

        } catch (\Exception $e) {

            Log::info($e->getMessage());
        }
    }

}



if (! function_exists('calculateStoreWallet')) {
    function calculateStoreWallet(Store $store ) {
        $allvouchers = Voucher::where('user_id', $store->user->id ?? null)->where('belongsToStoreWallet', 1)->sum('amount');
        $paid = Voucher::where('user_id', $store->user->id ?? null)->where('belongsToStoreWallet', 1)->where('type', 'payment')->sum('amount');
        $fees = getStoreFees($store->id, [100, -100], 1);

        $outstandingBalance = getStoreFees($store->id, [10, 20, 35, 65], 1);


        return [
            'allvouchers' => $allvouchers,
            'paid' => $paid,
            'fees' => $fees,
            'outstandingBalance' => $outstandingBalance,
            'balance' => - $allvouchers - $fees,
            'freeBalance' => - $allvouchers - $fees - $outstandingBalance
        ];
    }
}

if (! function_exists('getStoreDues')) {
    function getStoreDues(Store $store ) {
       return calculateStoreDues([
            'fees' => getStoreFees($store->id),
            'cod' => Financial::whereHas('shipment', function ($query) use ($store) {
                        $query->where('status', 100)
                        ->where('store_id', $store->id);
                })->sum('cod'),
            'receipt' => $store->user->vouchers()
                ->where('amount', '<', 0)
                ->where('belongsToStoreWallet', 0)
                ->sum('amount'),
            'payment' => $store->user->vouchers()
                ->where('amount', '>', 0)
                ->where('belongsToStoreWallet', 0)
                ->sum('amount'),
            ]);
    }
}



if (! function_exists('getSettingValue')) {
    function getSettingValue($o) { // $o mean $option
        if ($o === 'vat' &&  !Setting::where('option', $o)->exists())
            return 15 ;
        if (Setting::where('option', $o)->exists())
            return Setting::where('option', $o)->first()->value ;
        if($o === '')
        return null ;
    }
}

if (! function_exists('webhookCall')) {
    function webhookCall($shipment, $webhookDetails) {
        $shipmentStatus = [
            '10' => trans('the_shipment_has_been_created'),
            '20' => trans('Shipment_received'),
            '35' => trans('processing_in_progress'),
            '65' => trans('Prepared_awaiting_delivery'),
            '100' => trans('delivered'),
            '-100' => trans('audit'),
            '-1' => trans('Shipment_canceled')
        ];

        if ($webhookDetails && $webhookDetails->active && filter_var($webhookDetails->url, FILTER_VALIDATE_URL)){
            WebhookCall::create()
            ->url($webhookDetails->url)
            ->payload(['event' => 'shipment.status.updated',
                'shipment' => [
                'number' => $shipment->number,
                'new_status' => [
                    'code' => $shipment->status,
                    'note' => $shipmentStatus[$shipment->status]
                ]
            ]])
            ->useSecret($webhookDetails->secret)
            ->dispatch();
        }


    }
}

if (! function_exists('getMarketerDues')) {
    function getMarketerDues($marketer ) {
        return number_format((float)$marketer->user->vouchers()->sum('amount') - (float)$marketer->commissions()->sum('amount') , 2, '.', '');
    }
}


if (! function_exists('createInvoice')) {
    function createInvoice($store, $from, $to ) {
        try {

            DB::beginTransaction();

            $number = DB::table('invoices')->whereYear('created_at', '=', date("Y"))->max('number') + 1;

            $fees = Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) '));

            $tax = $fees - $fees / ((float)getSettingValue('vat') * 0.01 + 1);

            $invoice = $store->invoices()->create([
                'number' => $number,
                'user_id' => null,
                'start_date' => $from,
                'end_date' => date('Y-m-d', strtotime($to  . "-1 day" )),
                'fees' => $fees,
                'tax' => $tax,
                'cod' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                    return $query->where('store_id', $store->id)->whereIn('status', [100])->whereBetween('updated_at', [$from, $to]);
                })->sum('cod'),
            ]);

            Financial::whereHas('shipment', function ($query) use ($store , $from, $to) {
                $query->whereIn('status', [100, -100])
                    ->where('store_id', $store->id)
                    ->whereBetween('updated_at', [$from, $to]);
                })->update(['invoice_id' => $invoice->id]);

            if ($invoice->fees == 0 && $invoice->cod == 0)
                DB::rollback();
            else
                DB::commit();



        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollback();
        }

    }
}






function translations($json)
{
    if(!file_exists($json)) {
	return [];
    }
    return json_decode(file_get_contents($json), true);
}
