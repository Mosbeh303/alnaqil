<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Integrations\Torood\ParcelHelper;
use App\Models\Shipment;
use App\Models\Financial;

use App\Models\ShipmentTrack;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Lang;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $shipmentStatusAr;
    protected $shipmentStatusEn;

    public function __construct()
    {
        $this->shipmentStatusAr = [
            '10' => trans('the_shipment_has_been_created'),
            '20' => trans('Shipment_received'),
            '35' => trans('processing_in_progress'),
            '65' => trans('Prepared_awaiting_delivery'),
            '100' => trans('delivered'),
            '-100' => trans('audit'),
            '-1' => trans('canceled'),
        ];

        $this->shipmentStatusEn = [
            '10' => Lang::get('the_shipment_has_been_created',[],'en'),
            '20' => Lang::get('Shipment_received',[],'en'),
            '35' => Lang::get('processing_in_progress',[],'en'),
            '65' => Lang::get('Prepared_awaiting_delivery',[],'en'),
            '100' => Lang::get('delivered',[],'en'),
            '-100' => Lang::get('audit',[],'en'),
            '-1' =>  Lang::get('canceled',[],'en'),
        ];
    }

    public function index()
    {
        try {
            $shipments = Shipment::where('store_id', auth('sanctum')->user()->store->id)->get();

            $data = ['shipments' => []];
            foreach ($shipments as $shipment) {
                array_push($data['shipments'], [
                    "shipment_number" => $shipment->number,
                    'receiverName' => $shipment->receiver_name,
                    'receiverPhone' => $shipment->receiver_phone,
                    'cost' => calculateFees($shipment->financial),
                    'status' => [
                        'code' => $shipment->status,
                        'note' => $this->shipmentStatusAr[$shipment->status],
                        'note_en' => $this->shipmentStatusEn[$shipment->status],
                    ],
                ]);
            }

            $response = [
                'status' => true,
                'message' => "All Shipments",
                'data' => $data,
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
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function check(Request $request)
    {


        try {

            Validator::make($request->all(),
        [
            [
                'receiver' => 'required|string|max:255',
                'city' => 'required',
                'neighborhood' => 'required|string|max:255',
                'receiverPhone' => 'required|numeric|digits:10',
               // 'cod' => 'numeric|nullable',
                'details' => 'required|string|max:255',

            ]
        ]);



        $store = auth('sanctum')->user()->store;

        $number = str_pad(mt_rand(1, 999999999), 10, '1', STR_PAD_LEFT); //! Should review


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

                $shipment = new Shipment([
                    'number' => $number,
                    'status' => 10,
                    'receiver_name' => $request->receiver,
                    'city' => $request->city,
                    'street' => $request->street,
                    'neighborhood' => $request->neighborhood,
                    'receiver_phone' => $request->receiverPhone,
                    'details' => $request->details,
                    'weight' => $request->weight ?? 0.5,
                    'source' => 'Mobile',
                ]);

                $financial = new Financial([
                    'cod' => $cod,
                    'base_fee' => $base_fee,
                    'refrigerated_transport_fee' => $refrigerated_transport_fee,
                    'exchange_fee' => $exchange_fee,
                    'cod_fee' => $cod_fee,
                    'extra_fees' => $return_fee,
                    'tax' => null,
                    'wallet' => $wallet,
                ]);


                $response = [
                    'code' => 201,
                    'success' => true,
                    'message' => "Shipment details",
                    'data' => [
                        // "tracking_link" => $url . "tracking?number=" . $shipment->number,
                        "shipment" => $shipment,
                        "financial" => $financial,
                        // "pdf_label" => $url . $shipment->number . "/policy",
                        // "shipment_type" => $type,
                        'cost' => calculateFees($financial),
                        'balance' => calculateStoreWallet($store)['balance'],
                    ],
                ];

                //ParcelHelper::create($shipment);

                return response()->json($response, 201);
            } else {
                return response()->json([
                    'code' => 401,
                    'success' => false,
                    'message' => "Store not found",
                    'data' => null,
                ], 401);
            }
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);

            Log::critical("Something went wrong, in API/ShipmentController  function store");
            Log::error($e);
        }
    }


    public function store(Request $request)
    {
        $requestData = $request->all();

        $shipmentData = $requestData['shipment'];
        $financialData = $requestData['financial'];

        $shipmentValidator = Validator::make($shipmentData, [
            'number' => 'required|string|max:255',
            'status' => 'required|numeric',
            'receiver_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'receiver_phone' => 'required|numeric|digits:10',
            'details' => 'required|string|max:255',
            'weight' => 'required|numeric|max:255',
            'source' => 'required|string|max:255',
        ]);

        $financialValidator = Validator::make($financialData, [
            'cod' => 'numeric|nullable',
            'base_fee' => 'required|numeric',
            'refrigerated_transport_fee' => 'numeric',
            'exchange_fee' => 'numeric',
            'cod_fee' => 'numeric',
            'extra_fees' => 'numeric',
            // Add validation rules for 'tax' and 'wallet' if needed
        ]);

        if ($shipmentValidator->fails() || $financialValidator->fails()) {
            return response([
                'code' => 403,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => [$shipmentValidator, $financialValidator],
            ], 403);
        }

        $store = auth('sanctum')->user()->store;

        $number = str_pad(mt_rand(1, 999999999), 10, '1', STR_PAD_LEFT); // Should review

        DB::beginTransaction();

        try {
            if ($store) {
                $shipment = $store->shipments()->create($shipmentData);

                $financial = $shipment->financial()->create($financialData);


                $track = $shipment->shipmentTracks()->create([
                    'user_id' => auth('sanctum')->user()->id,
                    'status_after_action' => $shipment->status,
                    'action' => 'create',
                ]);

                DB::commit();

                $url = env('APP_URL');

                $response = [
                    'code' => 201,
                    'success' => true,
                    'message' => "Shipment created successfully",
                    'data' => [
                        "shipment" => [
                            // "tracking_link" => $url . "tracking?number=" . $shipment->number,
                            "shipment_number" => $shipment->number,
                            // "pdf_label" => $url . $shipment->number . "/policy",
                            // "shipment_type" => $type,
                            'cost' => calculateFees($shipment->financial),
                        ]
                    ],
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
            // Something went wrong
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
                        'note' => $this->shipmentStatusAr[$shipment->status],
                        'note_en' => $this->shipmentStatusEn[$shipment->status],
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

            Log::critical("Something went wrong, in API/ShipmentController , function show");
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

            Log::critical("Something went wrong, in API/ShipmentController  function cancel");
            Log::error($e);
        }
    }

    public function tracking(Request $request)
    {
        try {

            $number = $request->number;

            $shipment = Shipment::where('number', $number)->where('store_id', auth('sanctum')->user()->store->id)->first();

            if (!$shipment) {
                return response([
                    'code' => 401,
                    'success' => false,
                    'message' => "Shipment number is wrong",
                    'data' => null,
                ], 401);
            }

            //$url = env('APP_URL');

            $response = [
                'code' => 200,
                'success' => true,
                'message' => "Shipment tracking",
                'data' => [
                    "shipment" => [

                        "number" =>  $shipment->number,
                        "created_at" => $shipment->created_at->format('Y-m-d'),
                        "delivery_date" => $shipment->status == 100 ? $shipment->created_at->format('Y-m-d') : null,

                        "sender" => [
                            "name" => $shipment->store->name,
                        ],
                        'last_status' => [
                            'code' => $shipment->status,
                            'note' => $this->shipmentStatusAr[$shipment->status],
                            'note_en' => $this->shipmentStatusEn[$shipment->status],
                        ],
                        'tracking' => ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
                            return $query->where('number', $number);
                        })->where('action', '!=', 'update_phone')
                            ->where('action', '!=', 'update_warehouse')
                            ->where('action', '!=', 'update_operator')
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->map(fn ($track) => [
                                'date' => $track->created_at->format('Y-m-d H:i:s'),
                                'status' => [
                                    'code' => $track->status_after_action,
                                    'note' => $this->shipmentStatusAr[$track->status_after_action],
                                    'note_en' => $this->shipmentStatusEn[$track->status_after_action],

                                ]
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

            Log::critical("Something went wrong, in API/ShipmentController  function tracking");
            Log::error($e);
        }
    }
}
