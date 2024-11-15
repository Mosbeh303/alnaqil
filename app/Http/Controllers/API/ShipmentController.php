<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Integrations\Torood\ParcelHelper;
use App\Models\Shipment;
use App\Models\ShipmentTrack;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $shipmentStatus;
    public function __construct()
    {
        $this->shipmentStatus = [
            '10' => trans('the_shipment_has_been_created'),
            '20' =>trans('Shipment_received'),
            '35' =>trans('processing_in_progress'),
            '65' => trans('Prepared_awaiting_delivery'),
            '100' => trans('delivered'),
            '-100' => trans('audit'),
            '-1' => trans('canceled'),
        ];
    }

    public function index()
    {
        try {
            $shipments = Shipment::where('store_id', auth('sanctum')->user()->store->id)->get();
            $url = env('APP_URL');

            $data = ['shipments' => []];
            foreach ($shipments as $shipment) {
                array_push($data['shipments'], [
                    "tracking_link" => $url . "tracking?number=" . $shipment->number,
                    "shipment_number" => $shipment->number,
                    "pdf_label" => $url . $shipment->number . "/policy",
                    'cost' => calculateFees($shipment->financial),
                    'status' => [
                        'code' => $shipment->status,
                        'note' => $this->shipmentStatus[$shipment->status],
                    ],
                ]);
            }

            $response = [
                'code' => 200,
                'success' => true,
                'message' => "All Shipments",
                'data' => $data,
            ];

            return response($response, 200);
        } catch (\Exception$e) {
            // something went wrong
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string|max:255',
            'city' => 'required',
            'neighborhood' => 'required|string|max:255',
            'receiverPhone' => 'required|numeric|digits:10',
            'cod' => 'numeric|nullable',
            'details' => 'required|string|max:255',

        ]);

        $store = auth('sanctum')->user()->store;

        $number = str_pad(mt_rand(1, 999999999), 10, '1', STR_PAD_LEFT); //! Should review

        DB::beginTransaction();

        try {
            if ($store) {

                $city = \App\Models\City::where('name', $request->city)->first();
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

                $totalfees = $base_fee;

                $cod = $request->cod ? $request->cod : 0;
                $cod_fee = 0;
                if ($store->cod_active && $cod > 0) {
                    if ($cod > $store->fixed_amount_cod_fee && $store->fixed_amount_cod_fee !== null) {
                        $cod_fee = $cod * $store->cod_fee_percent / 100;
                    } else {
                        $cod_fee = $store->cod_fee;
                    }

                }

                $totalfees += $cod_fee;

                if ($request->exchange) {
                    $exchange_fee = $store->exchange_fee;
                    $totalfees += $store->exchange_fee;
                } else {
                    $exchange_fee = 0;
                }

                if ($request->refrigerated) {
                    $refrigerated_transport_fee = $store->refrigerated_transport_fee;
                    $totalfees += $store->refrigerated_transport_fee;
                } else {
                    $refrigerated_transport_fee = 0;
                }

                if ($request->return) {
                    $return_fee = $store->return_fee;
                    $totalfees += $store->return_fee;
                } else {
                    $return_fee = 0;
                }

                if (!$store->postpaid_active && $totalfees > calculateStoreWallet($store)['freeBalance'] && $totalfees > -getStoreDues($store)) {
                    return response([
                        'code' => 401,
                        'success' => false,
                        'message' => trans('balance_not_enough'),
                        'data' => null,
                    ], 401);
                }

                if (!$store->postpaid_active && $totalfees <= calculateStoreWallet($store)['freeBalance']) {
                    $wallet = 1;
                } else {
                    $wallet = 0;
                }

                $shipment = $store->shipments()->create([
                    'number' => $number,
                    'status' => 10,
                    'receiver_name' => $request->receiver,
                    'city' => $request->city,
                    'street' => $request->street,
                    'neighborhood' => $request->neighborhood,
                    'receiver_phone' => $request->receiverPhone,
                    'details' => $request->details,
                    'exchange' => $request->exchange ?? 0,
                    'refrigerated' => $request->refrigerated ?? 0,
                    'return_back' => $request->return ?? 0,
                    'weight' => $request->weight ?? 0.5,
                    'number_of_pieces' => $request->numberOfPieces ?? 1,
                    'source' => 'API',
                ]);

                $financial = $shipment->financial()->create([
                    'cod' => $cod,
                    'base_fee' => $base_fee,
                    'refrigerated_transport_fee' => $refrigerated_transport_fee,
                    'exchange_fee' => $exchange_fee,
                    'cod_fee' => $cod_fee,
                    'extra_fees' => $return_fee,
                    'tax' => null,
                    'wallet' => $wallet,
                ]);

                $track = $shipment->shipmentTracks()->create([
                    'user_id' => auth('sanctum')->user()->id,
                    'status_after_action' => $shipment->status,
                    'action' => 'create',
                ]);

                if ($request->pickupAddress){

                    Log::info($request->pickupAddress);

                    $pickupAddress = json_decode($request->pickupAddress, true);

                    Log::info($pickupAddress);

                    $shipment->pickupAddress()->create([
                        'store_id' => $shipment->store_id,
                        'name' => $pickupAddress['name'] ?? null ,
                        'city' => $pickupAddress['city'] ?? null  ,
                        'neighborhood' => $pickupAddress['neighborhood'] ?? null ,
                        'national_address' => $pickupAddress['nationalAddress'] ?? null ,
                        'full_address' => $pickupAddress['fullAddress'] ?? null ,
                        'phone' => $pickupAddress['phone'] ?? null ,
                    ]);
                }


                DB::commit();

                $url = env('APP_URL');

                $response = [
                    'code' => 201,
                    'success' => true,
                    'message' => "Shipment created successfully",
                    'data' => ["shipment" => [
                        "tracking_link" => $url . "tracking?number=" . $shipment->number,
                        "shipment_number" => $shipment->number,
                        "pdf_label" => $url . $shipment->number . "/policy",
                        // "shipment_type" => $type,
                        'cost' => calculateFees($shipment->financial),
                    ]],
                ];

                ParcelHelper::create($shipment);

                return response($response, 201);
            } else {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "Store not found",
                    'data' => null,
                ], 401);
            }
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);

            Log::critical("Something went wrong, in API/ShipmentController function store");
            Log::error($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($number)
    {
        try {
            $shipment = Shipment::where('number', $number)->where('store_id', auth('sanctum')->user()->store->id)->first();

            if (!$shipment) {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "Shipment number is wrong",
                    'data' => null,
                ], 401);
            }

            $url = env('APP_URL');

            $response = [
                'code' => 200,
                'success' => true,
                'message' => "Shipment details",
                'data' => ["shipment" => [
                    "tracking_link" => $url . "tracking?number=" . $shipment->number,
                    "shipment_number" => $shipment->number,
                    "pdf_label" => $url . $shipment->number . "/policy",
                    // "shipment_type" => $type,
                    'cost' => calculateFees($shipment->financial),
                    'status' => [
                        'code' => $shipment->status,
                        'note' => $this->shipmentStatus[$shipment->status],
                    ],
                ]],
            ];

            return response($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);

            Log::critical("Something went wrong, in API/ShipmentController, function show");
            Log::error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancel($number)
    {

        // $request->validate([
        //     'number' => 'required',
        // ]);

        DB::beginTransaction();

        try {

            $shipment = Shipment::where('number', $number)->where('store_id', auth('sanctum')->user()->store->id)->firstOrFail();

            // if (!$shipment)
            //     return Redirect::back()->with('error', ' عذرا! رقم الشحنة خاطئ.');

            if ($shipment->status == -1) {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "Shipment already cancelled",
                    'data' => null,
                ], 401);
            }

            if ($shipment->status != 10) {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "You cannot cancel an active shipment!",
                    'data' => null,
                ], 401);
            }

            $status_before_action = $shipment->status;

            $update = $shipment->update([
                'status' => -1,
            ]);

            if ($update) {
                $fees = calculateFees($shipment->financial);
                $tax = $fees - $fees / ((float) getSettingValue('vat') * 0.01 + 1);

                $shipment->financial()->update([
                    'tax' => $tax,
                ]);

                $track = $shipment->shipmentTracks()->create([
                    'user_id' => auth('sanctum')->user()->id,
                    'status_after_action' => $shipment->status,
                    'status_before_action' => $status_before_action,
                    'action' => 'update_status',
                ]);
                $toroodResponse = ParcelHelper::tracking($shipment);
            }

            DB::commit();

            return response([
                'code' => 200,
                'success' => true,
                'message' => "Shipment cancelled successfully",
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);

            Log::critical("Something went wrong, in API/ShipmentController function cancel");
            Log::error($e);
        }
    }

    public function tracking($number)
    {
        try {
            $shipment = Shipment::where('number', $number)->where('store_id', auth('sanctum')->user()->store->id)->first();

            if (!$shipment) {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "Shipment number is wrong",
                    'data' => null,
                ], 401);
            }

            $url = env('APP_URL');

            $response = [
                'code' => 200,
                'success' => true,
                'message' => "Shipment details",
                'data' => [
                    "shipment" => [
                        "shipment_number" => $shipment->number,
                        'last_status' => [
                            'code' => $shipment->status,
                            'note' => $this->shipmentStatus[$shipment->status],
                        ],
                        'tracking' => ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
                            return $query->where('number', $number);
                        })->where('action', '!=', 'update_phone')
                            ->where('action', '!=', 'update_warehouse')
                            ->where('action', '!=', 'update_operator')
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->map(fn($track) => [
                                'date' => $track->created_at->format('Y-m-d H:i:s'),
                                'status' => [
                                    'code' => $track->status_after_action,
                                    'note' => $this->shipmentStatus[$track->status_after_action],
                                ],
                            ]),

                    ],
                ],
            ];

            return response($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);

            Log::critical("Something went wrong, in API/ShipmentController function tracking");
            Log::error($e);
        }
    }
}
