<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\Financial;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\Store;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        if (Auth::user()->can('isAdminOrEmployee')) {
            return Inertia::render('Dashboard', [
                'shipmentsCount' => [
                    'created' => $this->getCountShipment(10),
                    'received' => $this->getCountShipment(20),
                    'processing' => $this->getCountShipment(35),
                    'progress' => $this->getCountShipment(65),
                    'completed' => $this->getCountShipment(100),
                    'returned' => $this->getCountShipment(-100),
                    'canceled' => $this->getCountShipment(-1),
                ],
                'stats' => [
                    // 'sms' => explode(":", file_get_contents("https://www.hisms.ws/api.php?get_balance&username=966538787107&password=Qq1234512345"))[1],
                    'sms' => 0,
                    'shipments' => $this->getCountShipment(),
                    'operators' => User::where('type', 'operator')->count(),
                    'clients' => User::where('type', 'client')->count(),
                ],

                'shipments' => Shipment::with('financial')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->map(fn($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                        'shipping_date' => $shipment->shipping_date ? $shipment->shipping_date : null,
                        'fees' => calculateFees($shipment->financial),
                        'cod' =>( $shipment->financial->cod ??0) + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'storeName' => $shipment->store->name,
                        'city' => $shipment->city,
                        'neighborhood' => $shipment->neighborhood,
                        'warehouseLocation' => $shipment->warehouse_location,
                        'status' => $shipment->status,
                        'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),

                    ]),

                'stores' => Store::orderBy('id', 'desc')
                    ->take(10)
                    ->get()
                    ->map(fn($store) => [
                        'id' => $store->id,
                        'name' => $store->name,
                        'ownerName' => $store->user->name,
                        'status' => $store->status,
                        'city' => $store->city,
                        'neighborhood' => $store->neighborhood,
                        'shipments' => $store->shipments->count(),
                    ]),
                'monthlyShipments' => Shipment::whereIn('status', [100, -100])
                    ->select(DB::raw('count(id) as `data`'), DB::raw('MONTH(updated_at) as month'))
                    ->groupBy('month')
                    ->get(),
                'monthlyProfit' => Financial::WhereHas('shipment', function ($query) {
                    $query->whereIn('status', [100, -100]);
                })->select(DB::raw('sum(tax) as `tax`, sum(base_fee) as `base_fee`, sum(exchange_fee) as `exchange_fee` , sum(refrigerated_transport_fee) as `refrigerated_transport_fee`, sum(extra_fees) as `extra_fees`, count(id) as `count`'), DB::raw('MONTH(updated_at) month'))
                    ->groupBy('month')
                    ->get()
                    ->map(fn($financial) => [
                        'data' => calculateFees($financial),
                        'month' => $financial->month,
                    ]),
            ]);
        }

        if (Auth::user()->can('isClient')) {
            return Inertia::render('Dashboard/ClientDashboard', [
                'shipmentsCount' => [
                    'created' => $this->getCountShipment(10, Auth::user()->store->id),
                    'received' => $this->getCountShipment(20, Auth::user()->store->id),
                    'processing' => $this->getCountShipment(35, Auth::user()->store->id),
                    'progress' => $this->getCountShipment(65, Auth::user()->store->id),
                    'completed' => $this->getCountShipment(100, Auth::user()->store->id),
                    'returned' => $this->getCountShipment(-100, Auth::user()->store->id),
                    'canceled' => $this->getCountShipment(-1, Auth::user()->store->id),
                ],
                'stats' => [
                    'allShipments' => $this->getCountShipment(null, Auth::user()->store->id),
                    'dues' => getStoreDues(Auth::user()->store),
                    'wallet' => calculateStoreWallet(Auth::user()->store),
                ],
                'shipments' => Shipment::with('financial')
                    ->where('store_id', Auth::user()->store->id)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->map(fn($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                        'shipping_date' => $shipment->shipping_date,
                        'fees' => calculateFees($shipment->financial),
                        'cod' => $shipment->financial->cod + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'city' => $shipment->city,
                        'neighborhood' => $shipment->neighborhood,
                        'street' => $shipment->street,
                        'warehouseLocation' => $shipment->warehouse_location,
                        'status' => $shipment->status,
                        'details' => $shipment->details,
                        'notice' => $shipment->noticeForClient ? $shipment->noticeForClient->notice : trans('there_is_no_notes'),

                    ]),

                'cities' => City::where('type', 'shipping_cities')->where('active', 1)->get() ?? [trans('alriyadh')],
                'districts' => District::all(),
                'monthlyShipments' => Shipment::whereIn('status', [100, -100])
                    ->where('store_id', Auth::user()->store->id)
                    ->select(DB::raw('count(id) as `data`'), DB::raw('MONTH(updated_at) month'))
                    ->groupBy('month')
                    ->get(),
            ]);
        }

        if (Auth::user()->can('isOperator')) {
            return Inertia::render('Dashboard/OperatorDashboard', [
                'filters' => RequestFacade::all('search'),
                'shipments' => Shipment::with('financial')
                    ->when(($request->search == null || Auth::user()->operator->type == 'del'), function ($query, $status) {
                        $query->where('operator_id', Auth::user()->operator->id ?? 0);
                    })
                    ->orderBy('created_at', 'desc')
                    ->filter(RequestFacade::only('search'))
                    ->paginate(15)
                    ->withQueryString()
                    ->through(fn($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'cod' => $shipment->financial->cod + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'storeName' => $shipment->store->name,
                        'city' => $shipment->city,
                        'neighborhood' => $shipment->neighborhood,
                        'status' => $shipment->status,
                        'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                        'details' => $shipment->details,
                    ]),
                'stats' => [
                    'dues' =>  Auth::user()->operator ? Auth::user()->operator->unpaid_dues :  0,
                    'completedShipments' => Auth::user()->shipmentTracks()->where('action', 'completed')->count(),
                    'returnedShipments' => Auth::user()->shipmentTracks()->where('action', 'returned')->count(),
                ],
                'operator_id' => Auth::user()->operator->id ?? 0,
                'whatsappMessage' => Setting::where('option', 'whatsapp_message')->exists() ? str_replace("\n", "%0a‎", Setting::where('option', 'whatsapp_message')->first()->value) : null,
            ]);
        }

        if (Auth::user()->can('isMarketer')) {
            return Inertia::render('Dashboard/MarketerDashboard', [

                'stats' => [
                    'dues' =>  getMarketerDues(Auth::user()->marketer),
                    'shipments_number' => Auth::user()->marketer->commissions()->count(),
                    'stores_number' => Auth::user()->marketer->stores()->count(),
                ],
                'cities' => City::where('type', 'shipping_cities')->where('active', 1)->get() ?? [trans('alriyadh')],
                'districts' => District::all(),
                'stores' => Store::where('marketer_id', Auth::user()->marketer->id)
                ->get()
                ->map(function ($store) {
                    return [
                        'id' => $store->id,
                        'name'  => $store->id . '- ' . $store->name,
                    ];
                })
                ->all(),
                 'shipments' => Shipment::with('financial')
                //      ->where('store_id', Auth::user()->store->id)
                      ->orderBy('created_at', 'desc')
                      ->take(10)
                      ->get()
                      ->map(fn($shipment) => [
                          'id' => $shipment->id,
                          'number' => $shipment->number,
                          'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                          'shipping_date' => $shipment->shipping_date,
                          'fees' => calculateFees($shipment->financial),
                          'cod' => $shipment->financial->cod + 0,
                          'receiverPhone' => $shipment->receiver_phone,
                          'receiverName' => $shipment->receiver_name,
                          'city' => $shipment->city,
                          'neighborhood' => $shipment->neighborhood,
                          'street' => $shipment->street,
                          'warehouseLocation' => $shipment->warehouse_location,
                          'status' => $shipment->status,
                          'details' => $shipment->details,
                          'notice' => $shipment->noticeForClient ? $shipment->noticeForClient->notice : trans('there_is_no_notes'),

                      ]),

            ]);
        }



    }

    public function getCountShipment($status = null, $id = null)
    {
        if ($id) {
            if ($status == null) {
                return Shipment::where('store_id', $id)->count();
            }

            return Shipment::where('store_id', $id)->where('status', $status)->count();
        } else {
            if ($status == null) {
                return Shipment::count();
            }

            return Shipment::where('status', $status)->count();
        }

    }
}