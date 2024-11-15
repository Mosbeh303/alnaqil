<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Inertia\Inertia;
use Auth;
use Redirect;
use App\Integrations\Torood\ParcelHelper;
use App\Integrations\Salla\SallaHelper;
use App\Integrations\Zid\ZidHelper;
use App\Jobs\SendSms;
use Illuminate\Support\Facades\Log;
use App\Models\Receiver;
use Illuminate\Support\Facades\Url;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class OperationController extends Controller
{
    public function received(Shipment $shipment = null, Request $request)
    {
        // $receiver = Receiver::where('phone', $shipment->receiver_phone)->first();
        // $url = url('receivers/' . base64_encode($receiver->id) . '/create-additional-data');
        //  dd($url);

        if ($shipment !== null && ($shipment->id ?? null)) {

            //$phone = '966' . substr($shipment->phone,1);

            $shipment->update([
                'operator_id' => Auth::user()->operator->id ?? null,
                'employee_id' => null,
            ]);

            $update = $shipment->update([
                'status' => 20
            ]);


            if ($update) {

                SendSms::dispatch($shipment);

                // Tracking
                $this->tracking('recieve', $shipment, 10);
                ParcelHelper::tracking($shipment);

                if ($shipment->salla_order_id)
                    SallaHelper::updateShipmentStatus($shipment);

                if ($shipment->zid_order_id)
                    ZidHelper::updateOrderStatus($shipment);

                return Redirect::back()->with('success', trans('Shipment_received'));
            }
        } else {
            $request->validate([
                'all' => 'required',
            ]);

            $shipments_numbers = array_unique($request->all);

            foreach ($shipments_numbers as $number) {

                $sh = Shipment::where('number', $number)->where('status', 10)->first();

                if ($sh) :
                    $update = $sh->update([
                        'status' => 20,
                        'operator_id' => Auth::user()->operator->id,
                        'employee_id' => null,
                    ]);

                    if ($update) {

                        //$phone = '966' . substr($sh->phone,1);

                        SendSms::dispatch($sh);

                        // Tracking
                        $this->tracking('recieve', $sh, 10);

                        $toroodResponse = ParcelHelper::tracking($sh);

                        if ($shipment->salla_order_id)
                            SallaHelper::updateShipmentStatus($shipment);

                        if ($shipment->zid_order_id)
                            ZidHelper::updateOrderStatus($shipment);
                    }
                endif;
            }

            return Inertia::render('Operators/Receive', [
                'shipments' => Shipment::with('financial')
                    ->whereIn('number', $request->all)
                    ->whereIn('status', [10, 20])
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'cod' => $shipment->financial->cod + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'city' => $shipment->city,
                        'neighborhood' => $shipment->neighborhood,
                        'status' => $shipment->status,
                        'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                    ]),
                'received' => true,
                'numbers'  => $request->all,
            ]);
        }
    }

    public function processing(Shipment $shipment)
    {
        if (!Auth::user()->operator->type == 'del')
            $shipment->update(
                [
                    'operator_id' => Auth::user()->operator->id,
                    'employee_id' => null,
                ]
            );

        $status_before_action = $shipment->status;

        $update = $shipment->update([
            'status' => 35
        ]);

        if ($update) {
            // Tracking
            $this->tracking('processing', $shipment, $status_before_action);
            $toroodResponse = ParcelHelper::tracking($shipment);
            if ($shipment->salla_order_id)
                SallaHelper::updateShipmentStatus($shipment);

            if ($shipment->zid_order_id)
                ZidHelper::updateOrderStatus($shipment);

            return Redirect::back();
        }
    }

    public function assign(Shipment $shipment = null, Request $request)
    {

        if ($shipment != null) {

            $shipment->update(
                [
                    'operator_id' => Auth::user()->operator->id,
                    'employee_id' => null,
                ]
            );

            $update = $shipment->update([
                'status' => 65
            ]);

            if ($update) {
                // Tracking
                $this->tracking('assign', $shipment, 35);
                $toroodResponse = ParcelHelper::tracking($shipment);
                if ($shipment->salla_order_id)
                    SallaHelper::updateShipmentStatus($shipment);

                if ($shipment->zid_order_id)
                    ZidHelper::updateOrderStatus($shipment);
            }
        } elseif (Auth::user()->operator->type == 'vip') {
            $request->validate([
                'all' => 'required',
            ]);

            foreach ($request->all as $number) {

                $shipment = Shipment::where('number', $number)->where('status', 35)->first();

                if ($shipment) :
                    $shipment->update(
                        [
                            'operator_id' => Auth::user()->operator->id,
                            'employee_id' => null,
                        ]
                    );

                    $update = $shipment->update([
                        'status' => 65
                    ]);


                    if ($update) {
                        // Tracking
                        $this->tracking('assign', $shipment, 35);
                        $toroodResponse = ParcelHelper::tracking($shipment);
                        if ($shipment->salla_order_id)
                            SallaHelper::updateShipmentStatus($shipment);

                        if ($shipment->zid_order_id)
                            ZidHelper::updateOrderStatus($shipment);
                    }
                endif;
            }
        }

        return Redirect::back()->with('success', trans('shipments_received_from_store'));
    }

    public function completed(Shipment $shipment, Request $request)
    {

        if ($shipment->status != 100 && $shipment->status != -100 && $shipment->status != -1) {

            $shipment->update(
                [
                    'operator_id' => Auth::user()->operator->id,
                    'employee_id' => null,
                ]
            );

            $status_before_action = $shipment->status;

            $update = $shipment->update([
                'status' => 100
            ]);

            if ($update) {

                $fees = calculateFees($shipment->financial);
                //tax
                $tax = $fees - $fees / ((float)getSettingValue('vat') * 0.01 + 1);

                $shipment->financial()->update([
                    'payment_method' => $request->method,
                    'tax'            => $tax,
                ]);

             if (  $shipment->jtexpress) {

               $shipment->jtexpress->update([
                'from_us'=>true,

                 ]);

                }

                // daily operator shipments
                Auth::user()->operator->dailyShipments()->create(['shipment_id' => $shipment->id]);

                // add operator dues
                if ($request->method == 'cash')
                    Auth::user()->operator->increment('unpaid_dues', $shipment->financial->cod);




                //  Update store dues
                if ($shipment->financial->wallet) {
                    $shipment->store()->increment('dues', $fees);
                    $shipment->store()->decrement('dues', $shipment->financial->cod);
                }


                // Tracking
                $this->tracking('completed', $shipment, $status_before_action);
                $toroodResponse = ParcelHelper::tracking($shipment);
                if ($shipment->salla_order_id)
                    SallaHelper::updateShipmentStatus($shipment);

                if ($shipment->zid_order_id)
                    ZidHelper::updateOrderStatus($shipment);
            }

            return Redirect::back();
        }
    }

    public function returned(Shipment $shipment)
    {

        $shipment->update(
            [
                'operator_id' => Auth::user()->operator->id,
                'employee_id' => null,
            ]
        );

        $status_before_action = $shipment->status;

        $update = $shipment->update([
            'status' => -100
        ]);

        if ($update) {


            // daily operator shipments
            Auth::user()->operator->dailyShipments()->create(['shipment_id' => $shipment->id]);


            $fees = calculateFees($shipment->financial);
            //tax
            $tax = $fees - $fees / ((float)getSettingValue('vat') * 0.01 + 1);

            $shipment->financial()->update([
                'tax'            => $tax,
            ]);

            // Update store dues
            if ($shipment->financial->wallet)
                $shipment->store()->increment('dues', $fees);

            // Tracking
            $this->tracking('returned', $shipment, $status_before_action);
            $toroodResponse = ParcelHelper::tracking($shipment);
            if ($shipment->salla_order_id)
                SallaHelper::updateShipmentStatus($shipment);

            if ($shipment->zid_order_id)
                ZidHelper::updateOrderStatus($shipment);
        }



        return Redirect::back();
    }

    public function tracking($action, $shipment, $status_before_action)
    {
        $track = $shipment->shipmentTracks()->create([
            'user_id' => Auth::user()->id,
            'status_after_action' => $shipment->status,
            'status_before_action' => $status_before_action,
            'action' => $action,
        ]);
    }

    public function getReceiveShipments(Request $request)
    {

        if ($request->isMethod('post')) {

            // $request->validate([
            //     'all' => 'required',
            // ]);
            return Inertia::render('Operators/Receive', [
                'shipments' => Shipment::with('financial')
                    ->whereIn('number', $request->all)
                    ->when(Auth::user()->operator->type == 'rec' || Auth::user()->operator->type == 'vip', function ($query) {
                        $query->whereIn('status', [10, 20]);
                    })
                    //->where('operator_id', Auth::user()->operator->id)
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'cod' => $shipment->financial->cod + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'city' => $shipment->city,
                        'neighborhood' => $shipment->neighborhood,
                        'status' => $shipment->status,
                        'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                    ]),

            ]);
        }

        return Inertia::render('Operators/Receive');
    }
}
