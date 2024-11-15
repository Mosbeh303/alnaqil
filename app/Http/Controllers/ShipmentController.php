<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Shipment;
use App\Models\ShipmentTrack;
use App\Models\Operator;
use App\Models\Financial;
use App\Models\Setting;
use App\Models\City;
use App\Models\District;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Gate;
use Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use File;
use DB;
use View;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShipmentsImport;
use Illuminate\Support\Facades\Crypt;
use App\Integrations\Torood\ParcelHelper;
use App\Integrations\Salla\SallaHelper;
use App\Integrations\Zid\ZidHelper;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\WebhookCall;
use App\Integrations\Jtexpress\JtexpressHelper;
use App\Models\Receiver;
use App\Exports\JntExport;
use App\Exports\statementExport;
use App\Exports\summaryExport;
use App\Models\PickupAddress;

class ShipmentController extends Controller
{





    public function index()
    {
        $this->authorize('isEmployeeHasPermission', 'index_shipments');



        if (Gate::allows('isAdminOrEmployee'))
            return Inertia::render('Shipments/Index', [
                'filters' => RequestFacade::all('search', 'status'),
                'shipments' => Shipment::with('financial')
                    ->orderBy('status', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->filter(RequestFacade::only('search', 'status'))
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                        'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                        'shipping_date' =>  $shipment->shipping_date,
                        'fees' => calculateFees($shipment->financial),
                        'cod' => ($shipment->financial->cod ?? 0) + 0,
                        'receiverPhone' => $shipment->receiver_phone,
                        'storeName' => $shipment->store->name,
                        'city' => $shipment->city,
                        'street' => $shipment->street,
                        'neighborhood' => $shipment->neighborhood,
                        'warehouseLocation' => $shipment->warehouse_location,
                        'status' => $shipment->status,
                        'notice' => $this->notices($shipment) ? $this->notices($shipment)[0]['notice'] : trans('there_is_no_notes'),
                        'branchForEmployee' => $shipment->employee->branch ?? null,
                        'branchForOperator' => $shipment->operator->branch ?? null,
                        'exchange' => intval($shipment->exchange),
                        'refrigerated' => intval($shipment->refrigerated),
                        'return_back' => intval($shipment->return_back),
                        'details' => $shipment->details,
                        'order_id' => $shipment->order_id,
                        'prints' => $shipment->waybillPrints()->count(),
                        'operator' => $shipment->operator->user->name ?? null,
                    ]),
            ]);

        elseif (Gate::allows('isClient'))
            return Inertia::render('Shipments/Index', [
                'filters' => RequestFacade::all('search', 'status'),
                'shipments' => Shipment::with('financial')
                    ->where('store_id', Auth::user()->store->id)
                    ->orderBy('status', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->filter(RequestFacade::only('search', 'status'))
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
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
                        'notice' => $this->notices($shipment) ? $this->notices($shipment)[0]['notice'] : trans('there_is_no_notes'),
                        'exchange' => $shipment->exchange,
                        'refrigerated' => $shipment->refrigerated,
                        'return_back' => $shipment->return_back,
                        'details' => $shipment->details,
                        'order_id' => $shipment->order_id,
                    ]),
            ]);
    }

    public function query(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'query_shipments');
        return Inertia::render('Shipments/Query');
    }

    public function getQuery(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'query_shipments');
        $request->validate([
            'all' => 'required',
        ]);

        return Inertia::render('Shipments/Query', [
            'shipments' => Shipment::with('financial')
                // ->orderByName()
                ->whereIn('number', $request->all)
                ->orWhereIn('receiver_phone', $request->all)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'receiverPhone' => $shipment->receiver_phone,
                    'storeName' => $shipment->store->name,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'warehouseLocation' => $shipment->warehouse_location,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                    'exchange' => $shipment->exchange,
                    'refrigerated' => $shipment->refrigerated,
                    'return_back' => $shipment->return_back,

                ]),
        ]);
    }

    public function queryPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'query_shipments');

        $request->validate([
            'all' => 'required',
        ]);

        $searchArray = explode(",", $request->all);


        $shipments = Shipment::with('financial')
            // ->orderByName()
            ->whereIn('number', $searchArray)
            ->orWhereIn('receiver_phone', $searchArray)
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                'shipping_date' => $shipment->shipping_date,
                'fees' => calculateFees($shipment->financial),
                'cod' => $shipment->financial->cod + 0,
                'receiverPhone' => $shipment->receiver_phone,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,

            ]);

        $pdf = PDF::loadView('pdf/query', compact('shipments'), [], ['format'  => 'A4', 'mode' => 'landscape']);
        return $pdf->download('query.pdf');
    }

    public function summary(Request $request)
    {
        if ($request->for == 'jnt')
            $this->authorize('isEmployeeHasPermission', 'jnt_summary_shipments');
        else
            $this->authorize('isEmployeeHasPermission', 'summary_shipments');

        return Inertia::render('Shipments/Summary', [
            'jnt' => $request->for == 'jnt' ? true : false,
        ]);
    }

    public function getSummary(Request $request)
    {
        if ($request->jnt == 1)
            $this->authorize('isEmployeeHasPermission', 'jnt_summary_shipments');
        else
            $this->authorize('isEmployeeHasPermission', 'summary_shipments');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required',


        ]);

        return Inertia::render('Shipments/Summary', [
            'shipments' => Shipment::whereHas('financial', function ($query) use ($request) {
                    if ($request->receipt_type == 'all' && $request->type == '>') {
                        $query->where('cod', '>', 0);
                    } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                        $query->where('payment_method', $request->receipt_type);
                    } elseif ($request->type == '>=' || $request->type == '=') {
                        $query->where('cod', $request->type, 0);
                    }

                    return $query;
                })->when(($request->jnt ?? null), function ($query) {
                    $query->whereHas('jtexpress', function($subquery){
                        $subquery->where('from_us', false);
                    });
                })
                ->whereIn('status', [100, -100])
                ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                ->orderBy('updated_at')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $shipment->updated_at->format('Y-m-d H:i:s'),
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'receiverPhone' => $shipment->receiver_phone,
                    'receiverName' => $shipment->receiver_name,
                    'storeName' => $shipment->store->name,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'warehouseLocation' => $shipment->warehouse_location,
                    'status' => $shipment->status,
                    'exchange' => $shipment->exchange,
                    'refrigerated' => $shipment->refrigerated,
                    'return_back' => $shipment->return_back,
                    'billCode' => $shipment->jtexpress->bill_code ?? null,
                ]),
            'to' => $request->to,
            'from' => $request->from,
            'type' => $request->type,
            'receipt_type' => $request->receipt_type,
            'jnt' => $request->jnt,

        ]);
    }

    public function summaryPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'summary_shipments');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required'
        ]);

        $shipments = Shipment::whereHas('financial', function ($query) use ($request) {
                if ($request->receipt_type == 'all' && $request->type == '>') {
                    $query->where('cod', '>', 0);
                } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                    $query->where('payment_method', $request->receipt_type);
                } elseif ($request->type == '>=' || $request->type == '=') {
                    $query->where('cod', $request->type, 0);
                }

                return $query;
            })
            ->whereIn('status', [100, -100])
            ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
            ->orderBy('updated_at')
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => Gate::allows('isEmployeeHasPermission', 'fee_shipments') ? calculateFees($shipment->financial) : null,
                'cod' => $shipment->status == -100 ? 'returned' : $shipment->financial->cod + 0,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,
                'billCode' => $shipment->jtexpress->bill_code ?? null,
            ]);

        $from = $request->from;
        $to = $request->to;
        $type = $request->type;

        $sum = [
            'fees' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to) {
                return $query->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))]);
            })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')),
            'tax' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to) {
                return $query->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))]);
            })->sum('tax'),
            'cod' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to) {
                return $query->whereIn('status', [100])->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))]);
            })->sum('cod'),
        ];



        $pdf = PDF::loadView('pdf/summary', compact('shipments', 'from', 'to', 'sum', 'type'), [], ['format'  => 'A4', 'mode' => 'landscape']);
        return $pdf->download('summary.pdf');
    }



    public function statementExel(Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'accounting_statement_shipments');
        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required'
        ]);

        if ($request->store !== null)
            $request->validate(
                [
                    'store' => 'exists:App\Models\Store,id',
                ],
                [
                    'store.exists' => trans('store_number_does_not_exist')
                ]
            );

        $shipments = Shipment::whereHas('financial', function ($query) use ($request) {
            if ($request->receipt_type == 'all' && $request->type == '>') {
                $query->where('cod', '>', 0);
            } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                $query->where('payment_method', $request->receipt_type);
            } elseif ($request->type == '>=' || $request->type == '=') {
                $query->where('cod', $request->type, 0);
            }



            return $query;
        })
            ->whereIn('status', [100, -100])
            ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
            ->when($request->store, function ($query) use ($request) {
                $query->where('store_id', $request->store);
            })
            ->orderBy('updated_at')
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => Gate::allows('isEmployeeHasPermission', 'fee_shipments') ? calculateFees($shipment->financial) : null,
                'cod' => $shipment->status == -100 ? 'returned' : $shipment->financial->cod + 0,
                'payment_method' => $shipment->financial->payment_method,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,

            ]);










        return Excel::download(new statementExport($shipments), 'statement.xlsx');
    }


    public function summaryShipmentExel(Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'summary_shipments');
        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required'
        ]);

        if ($request->store !== null)
            $request->validate(
                [
                    'store' => 'exists:App\Models\Store,id',
                ],
                [
                    'store.exists' => trans('store_number_does_not_exist')
                ]
            );

        $shipments = Shipment::whereHas('financial', function ($query) use ($request) {
            if ($request->receipt_type == 'all' && $request->type == '>') {
                $query->where('cod', '>', 0);
            } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                $query->where('payment_method', $request->receipt_type);
            } elseif ($request->type == '>=' || $request->type == '=') {
                $query->where('cod', $request->type, 0);
            }



            return $query;
        })
            ->whereIn('status', [100, -100])
            ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
            ->when($request->store, function ($query) use ($request) {
                $query->where('store_id', $request->store);
            })
            ->orderBy('updated_at')
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => Gate::allows('isEmployeeHasPermission', 'fee_shipments') ? calculateFees($shipment->financial) : null,
                'cod' => $shipment->status == -100 ? 'returned' : $shipment->financial->cod + 0,
                'payment_method' => $shipment->financial->payment_method,
                'receiver_name' => $shipment->receiver_name,
                'receiver_phone' => $shipment->receiver_phone,

                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,

            ]);










        return Excel::download(new summaryExport($shipments), 'summary.xlsx');
    }



    public function jntSummaryExcel(Request $request) // jnt summary excel
    {
        $this->authorize('isEmployeeHasPermission', 'jnt_summary_shipments');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        $shipments = Shipment::whereHas('jtexpress')
            ->whereIn('status', [100, -100])
            ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
            ->orderBy('updated_at')
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'billCode' => $shipment->jtexpress->bill_code,
                'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => Gate::allows('isEmployeeHasPermission', 'fee_shipments') ? calculateFees($shipment->financial) : null,
                'cod' => $shipment->status == -100 ? 'returned' : $shipment->financial->cod + 0,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,
            ]);

        return Excel::download(new JntExport($shipments), 'jnt.xlsx');
    }

    public function statement(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'accounting_statement_shipments');

        return Inertia::render('Shipments/Statement');
    }

    public function getStatement(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'accounting_statement_shipments');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required'
        ]);

        if ($request->store !== null)
            $request->validate(
                [
                    'store' => 'exists:App\Models\Store,id',
                ],
                [
                    'store.exists' => trans('store_number_does_not_exist')
                ]
            );

        return Inertia::render('Shipments/Statement', [
            'shipments' => Shipment::whereHas('financial', function ($query) use ($request) {
                if ($request->receipt_type == 'all' && $request->type == '>') {
                    $query->where('cod', '>', 0);
                } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                    $query->where('payment_method', $request->receipt_type);
                } elseif ($request->type == '>=' || $request->type == '=') {
                    $query->where('cod', $request->type, 0);
                }



                return $query;
            })
                ->whereIn('status', [100, -100])
                ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                ->when($request->store, function ($query) use ($request) {
                    $query->where('store_id', $request->store);
                })
                ->orderBy('updated_at')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $shipment->updated_at->format('Y-m-d H:i:s'),
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'payment_method' => $shipment->financial->payment_method,
                    'receiverPhone' => $shipment->receiver_phone,
                    'receiverName' => $shipment->receiver_name,
                    'storeName' => $shipment->store->name,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'warehouseLocation' => $shipment->warehouse_location,
                    'status' => $shipment->status,
                    'exchange' => $shipment->exchange,
                    'refrigerated' => $shipment->refrigerated,
                    'return_back' => $shipment->return_back,
                ]),
            'to' => $request->to,
            'from' => $request->from,
            'type' => $request->type,
            'receipt_type' => $request->receipt_type,
            'store' => $request->store,
            'sum_cash' => Financial::whereHas('shipment', function ($query) use ($request) {
                return $query->where('status', 100)
                    ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->where('payment_method', 'cash')
                ->sum('cod'),
            'sum_epayment' => Financial::whereHas('shipment', function ($query) use ($request) {
                return $query->where('status', 100)
                    ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->where('payment_method', 'epayment')
                ->sum('cod'),
        ]);
    }

    public function statementPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'accounting_statement_shipments');
        $request->validate([
            'to' => 'required',
            'from' => 'required',
            'type' => 'required'
        ]);

        if ($request->store !== null)
            $request->validate(
                [
                    'store' => 'exists:App\Models\Store,id',
                ],
                [
                    'store.exists' => trans('store_number_does_not_exist')
                ]
            );

        $shipments = Shipment::whereHas('financial', function ($query) use ($request) {
            if ($request->receipt_type == 'all' && $request->type == '>') {
                $query->where('cod', '>', 0);
            } elseif ($request->receipt_type == 'cash' || $request->receipt_type == 'epayment' && $request->type == '>') {
                $query->where('payment_method', $request->receipt_type);
            } elseif ($request->type == '>=' || $request->type == '=') {
                $query->where('cod', $request->type, 0);
            }



            return $query;
        })
            ->whereIn('status', [100, -100])
            ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
            ->when($request->store, function ($query) use ($request) {
                $query->where('store_id', $request->store);
            })
            ->orderBy('updated_at')
            ->get()
            ->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => Gate::allows('isEmployeeHasPermission', 'fee_shipments') ? calculateFees($shipment->financial) : null,
                'cod' => $shipment->status == -100 ? 'returned' : $shipment->financial->cod + 0,
                'payment_method' => $shipment->financial->payment_method,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status,
                'exchange' => $shipment->exchange,
                'refrigerated' => $shipment->refrigerated,
                'return_back' => $shipment->return_back,

            ]);




        $from = $request->from;
        $to = $request->to;
        $type = $request->type;


        $sum = [
            'fees' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to, $request) {
                return $query->whereIn('status', [100, -100])
                    ->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')),

            'tax' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to, $request) {
                return $query->whereIn('status', [100, -100])
                    ->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });;
            })->sum('tax'),

            'cod' => Financial::where('cod', $request->type, 0)->whereHas('shipment', function ($query) use ($from, $to, $request) {
                return $query->whereIn('status', [100])
                    ->whereBetween('updated_at', [$from, date('Y-m-d', strtotime($to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->sum('cod'),

            'cod_cash'  => Financial::whereHas('shipment', function ($query) use ($request) {
                return $query->where('status', 100)
                    ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->where('payment_method', 'cash')
                ->sum('cod'),

            'cod_epayment' => Financial::whereHas('shipment', function ($query) use ($request) {
                return $query->where('status', 100)
                    ->whereBetween('updated_at', [$request->from, date('Y-m-d', strtotime($request->to . "+1 day"))])
                    ->when($request->store, function ($query) use ($request) {
                        $query->where('store_id', $request->store);
                    });
            })->where('payment_method', 'epayment')
                ->sum('cod')
        ];



        $pdf = PDF::loadView('pdf/statement', compact('shipments', 'from', 'to', 'sum', 'type'), [], ['format'  => 'A4', 'mode' => 'landscape']);
        return $pdf->download('statement.pdf');
    }

    public function create()
    {
        $this->authorize('isEmployeeHasPermission', 'create_shipments');

        return Inertia::render('Shipments/Create', [
            'stores' => Store::all()
            ->map(fn ($store) => [
                'value' => $store->id,
                'label'  => $store->id . '- ' . $store->name,
            ]),
            'cities' => City::where('type', 'shipping_cities')->where('active', 1)->get() ?? [trans('alriyadh')],
            'districts' => District::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_shipments');

        $request->validate(
            [
                'receiver' => 'required|string|max:255',
                'city' => 'required',
                'neighborhood' => 'required|string|max:255',
                'receiverPhone' => 'required|numeric||digits:10',
                'cod' => 'numeric|nullable',
                'ADP' => 'numeric|nullable',
                'details' => 'required|string|max:255',

            ],
            [
                'required' => trans('required_field')
            ]
        );
        if (Auth::user()->type == "marketer") {
            $store = Store::find($request->store);
            if ($store->marketer_id ?? null != Auth::user()->marketer->id)
                return Redirect::back()->with('error', trans('store_number_not_found'));
        }

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate(
                [
                    'store' => 'required|exists:App\Models\Store,id',
                ],
                [
                    'store.exists' => trans('store_number_does_not_exist')
                ]
            );
            $store = Store::find($request->store);
        }




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


            $cod = $request->cod  ? $request->cod : 0;
            $cod_fee = 0;
            if ($store->cod_active && $cod > 0) {
                if ($cod > $store->fixed_amount_cod_fee && $store->fixed_amount_cod_fee !== null)
                    $cod_fee = $cod * $store->cod_fee_percent / 100;
                else
                    $cod_fee = $store->cod_fee;
            }

            $totalfees += $cod_fee;

            if ($request->exchange) {
                $exchange_fee = $store->exchange_fee;
                $totalfees += $store->exchange_fee;
            } else
                $exchange_fee = 0;

            if ($request->refrigerated) {
                $refrigerated_transport_fee = $store->refrigerated_transport_fee;
                $totalfees += $store->refrigerated_transport_fee;
            } else
                $refrigerated_transport_fee = 0;

            if ($request->return) {
                $return_fee = $store->return_fee;
                $totalfees += $store->return_fee;
            } else
                $return_fee = 0;

            if (!$store->postpaid_active && $totalfees > calculateStoreWallet($store)['freeBalance'] && $totalfees > -getStoreDues($store))
                return Redirect::back()->with('error', trans('balance_not_enough'));

            if (!$store->postpaid_active && $totalfees <= calculateStoreWallet($store)['freeBalance'])
                $wallet = 1;
            else
                $wallet = 0;

            $shipment = $store->shipments()->create([
                'number' => $number,
                'status' => 10,
                'receiver_name' => $request->receiver,
                'city' => $request->city,
                'street' => $request->street,
                'neighborhood' => $request->neighborhood,
                'receiver_phone' => $request->receiverPhone,
                'details' => $request->details,
                'exchange' => $request->exchange,
                'refrigerated' => $request->refrigerated,
                'return_back' => $request->return,
                'weight' => $request->weight ?? 0.5,
                'number_of_pieces' => $request->numberOfPieces ?? 1,
                'order_id' => $request->order_id,

            ]);

            $financial = $shipment->financial()->create([
                'cod' => $cod,
                'ADP' => $request->ADP,
                'base_fee' => $base_fee,
                'refrigerated_transport_fee' => $refrigerated_transport_fee,
                'exchange_fee' => $exchange_fee,
                'cod_fee' => $cod_fee,
                'extra_fees' => $return_fee,
                'tax' => null,
                'wallet' => $wallet
            ]);

            $track = $shipment->shipmentTracks()->create([
                'user_id' => Auth::user()->id,
                'status_after_action' => $shipment->status,
                'action' => 'create',
            ]);

            $toroodResponse = ParcelHelper::create($shipment);

            // return     $toroodResponse ;
            //$jt = JtexpressHelper::createOrder($shipment);
            //return $jt;
            return Redirect::back()->with('success', trans('the_shipment_has_been_created'));
        }
    }

    public function import(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_shipments');

        $request->validate([
            'file' => 'required',
        ]);

        $file = $request->file;



        if ($file) {
            $j = 0;
            $si = new ShipmentsImport();
            Excel::import($si, $request->file('file')->store('temp'));
        } else {
            //no file was uploaded
            throw new \Exception('No file was uploaded');
        }

        // $count = count($importData_arr) ;

        return Redirect::back()->with('success', trans('improted') .  --$si->count . '/' . $si->j .  trans('shipments_successfully'));
        // return Redirect::back()->with('success', 'تم استيراد الشحنات');
    }

    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded'); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension'); //415 error
        }
    }

    public function show(Shipment $shipment)
    {
        $this->authorize('isEmployeeHasPermission', 'show_shipments');

        if (auth()->user()->type == "client" && $shipment->store_id != auth()->user()->store->id)
            abort(404);

        $notices = $this->notices($shipment);


        $receiver = Receiver::where('phone', $shipment->receiver_phone)->first();


        return Inertia::render('Shipments/Show', [
            'receiver' => $receiver,
            'shipment' => [
                'id' => $shipment->id,
                'slug' => Crypt::encryptString($shipment->number),
                'number' => $shipment->number,
                'status' => $shipment->status,
                'storeCity' => $shipment->store->city,
                'storeType' => $shipment->store->type,
                'storeName' => $shipment->store->name,
                'storeNeighborhood' => $shipment->store->neighborhood,
                'storePhone' => $shipment->store->user->phone,
                'details' => $shipment->details,
                'receiverName' => $shipment->receiver_name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'street' => $shipment->street,
                'receiverPhone' => $shipment->receiver_phone,
                'cod' => $shipment->financial->cod,
                'failed' => $shipment->failed,
                'created_at' => $shipment->created_at->format('Y-m-d'),
                'return' => intval($shipment->return_back),
                'exchange' => intval($shipment->exchange),
                'refrigerated' => intval($shipment->refrigerated),
                'weight' => $shipment->weight,
                'numberOfPieces' => $shipment->number_of_pieces,
                'jtBillCode' => $shipment->jtexpress->bill_code ?? null,
                'pickupAddress' => $shipment->pickupAddress
            ],
            'notices' => $notices,
            'failedNoticeOptions' => Setting::where('option', 'failed_notices')->exists() ? json_decode(Setting::where('option', 'failed_notices')->first()->value) : null,

        ]);
    }

    private function notices($shipment)
    {
        if (auth()->user()->type !== 'client') {
            $notices1 = $shipment->notices()->get()->map(fn ($notice) => [
                'id' => $notice->id,
                'notice' => $notice->notice,
                'user_id' => $notice->user->id,
                'date' => $notice->created_at->format('Y-m-d H:m:s'),
                'user' => $notice->user->username
            ]);
            $notices2 =     $shipment->shipmentTracks()->where('action', 'failed_delivery')->get()->map(fn ($notice) => [
                'id' => $notice->id,
                'notice' => $notice->notice,
                'user_id' => $notice->user->id,
                'date' => $notice->created_at->format('Y-m-d H:m:s'),
                'user' => $notice->user->username,
            ]);
            $notices = $notices1->toBase()->merge($notices2)->sortByDesc('date');
        } else
            $notices = $shipment->shipmentTracks()->where('action', 'failed_delivery')->orderBy('created_at', 'DESC')->get()->map(fn ($notice) => [
                'id' => $notice->id,
                'notice' => $notice->notice,
                'user_id' => $notice->user->id,
                'date' => $notice->created_at->format('Y-m-d H:m:s'),
                'user' => $notice->user->username,
            ]);

        $notices = $notices->values()->all();
        return $notices;
    }

    public function policy($slug)
    {
        $shipment = Shipment::where('number', $slug)->first();

        if ($shipment && auth()->check()) {
            $shipment->waybillPrints()->create([
                'user_id' => auth()->user()->id
            ]);
        }

        if (!$shipment)
            return view('404-shipment');
        else
            $pdf = PDF::loadView('bill', compact('shipment'));
        return $pdf->stream($slug . '.pdf');
    }

    public function exchangedBillPdf($slug)
    {
        $shipment = Shipment::where('number', $slug)->first();

        if (!$shipment)
            return view('404-shipment');
        else
            $pdf = PDF::loadView('pdf.exchange-bill', compact('shipment'), [
                'format' => 'A4-L',
                'orientation' => 'L'
            ]);


        return $pdf->download($slug . '.pdf');
    }



    public function edit(Shipment $shipment)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_shipments');

        if ($shipment->status == 100 || $shipment->status == -100 || auth()->user()->type === "client")
            abort(403);

        return Inertia::render('Shipments/Edit', [
            'shipment' => $shipment,
            'financial' => $shipment->financial,
            'refrigerated_transport_fee' => $shipment->store->refrigerated_transport_fee,
            'exchange_fee' => $shipment->store->exchange_fee,
            'return_fee' => $shipment->store->return_fee,
            'cod_fee' => $shipment->store->cod_fee,
            'cod_active' => $shipment->store->cod_active,
            'fixed_amount_cod_fee' => $shipment->store->fixed_amount_cod_fee,
            'cod_fee_percent' => $shipment->store->cod_fee_percent,
            'original_fees'   => calculateFees($shipment->financial),
            'cities' => City::where('type', 'shipping_cities')->where('active', 1)->get() ?? [trans('alriyadh')],
            'neighborhoods' => Setting::where('option', 'shipping_neighborhood')->exists() ? json_decode(Setting::where('option', 'shipping_neighborhood')->first()->value) : [],
        ]);
    }

    public function update(Request $request, Shipment $shipment)
    {

        $this->authorize('isEmployeeHasPermission', 'edit_shipments');



        if ($shipment->status == 100 || $shipment->status == -100 || auth()->user()->type === "client")
            abort(403, trans('Completed_or_returned_shipments_cannot_be_modified'));

        $request->validate([
            'receiver' => 'required|string|max:255',
            'city' => 'required',
            'neighborhood' => 'required|string|max:255',
            'receiverPhone' => 'required|numeric||digits:10',
            'cod' => 'required|numeric',
            'ADP' => 'nullable|numeric',
            'store' => 'required|exists:App\Models\Store,id',
        ], [
            'store.exists' => trans('store_number_does_not_exist')
        ]);

        $original = (object) array_merge((array) $shipment->toArray(), (array) $shipment->financial->toArray());

        $update1 = $shipment->update([
            'store_id' => $request->store,
            'receiver_name' => $request->receiver,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'street' => $request->street,
            'receiver_phone' => $request->receiverPhone,
            'details' => $request->details,
            'exchange' => $request->exchange,
            'refrigerated' => $request->refrigerated,
            'return_back' => $request->return,
            'weight' => $request->weight ?? 0.5,
            'number_of_pieces' => $request->numberOfPieces ?? 1,
            'order_id' => $request->order_id,

        ]);

        if ($update1) {
            $exchange_fee = 0;
            if ($request->exchange)
                $exchange_fee = $shipment->store->exchange_fee;

            $refrigerated_transport_fee = 0;
            if ($request->refrigerated)
                $refrigerated_transport_fee = $shipment->store->refrigerated_transport_fee;


            $return_fee = 0;
            if ($request->return)
                $return_fee = $shipment->store->return_fee;

            $cod = $request->cod  ? $request->cod : 0;
            $cod_fee = 0;

            if ($shipment->store->cod_active && $request->cod > 0) {
                if ($cod > $shipment->store->fixed_amount_cod_fee && $shipment->store->fixed_amount_cod_fee !== null)
                    $cod_fee = $cod * $shipment->store->cod_fee_percent / 100;
                else
                    $cod_fee = $shipment->store->cod_fee;
            }


            $financial = Financial::where('shipment_id', $shipment->id)->first();

            $additional_fees = 0 ;
            $extra_weight = $financial->shipment->weight - $financial->base_weight;
            if ($extra_weight > 0){
              $additional_fees =   ceil($extra_weight) * $financial->addtional_fee_per_kg;
            }

            $update2 = $financial->update([
                'cod' => $request->cod,
                'ADP' => $request->ADP,
                'base_fee' => $request->base_fee,
                'refrigerated_transport_fee' => $refrigerated_transport_fee,
                'exchange_fee' => $exchange_fee,
                'extra_fees' => $return_fee,
                'cod_fee' => $cod_fee,
                'addtional_fees_extra_weight' => $additional_fees
            ]);

            $track = $shipment->shipmentTracks()->create([
                'user_id' => auth()->user()->id,
                'status_after_action' => $shipment->status,
                'status_before_action' => $shipment->status,
                'action' => 'update',
                'changes' => json_encode((object) array_merge((array) $shipment->getChanges(), (array) $financial->getChanges())),
                'original' => json_encode($original)
            ]);

            $updateFinancial = ParcelHelper::updateFinancial($shipment);
            $updateDetails = ParcelHelper::updateDetails($shipment);
            $updateShipper = ParcelHelper::updateShipper($shipment);
            $updateConsignee = ParcelHelper::updateConsignee($shipment);

            return Redirect::route('shipments.edit', $shipment)->with('success', trans('shipment_updated_successfully'));
        } else {
            return back()->with('error', trans('Somthing_wrong'));
        }
    }

    public function updatePhone(Request $request, Shipment $shipment)
    {
        $this->authorize('isEmployeeHasPermission', 'show_shipments');

        $request->validate([
            'phone' => 'required|numeric||digits:10',
        ]);


        $shipment->update([
            'receiver_phone' => $request->phone,
        ]);

        $track = $shipment->shipmentTracks()->create([
            'user_id' => Auth::user()->id,
            'status_after_action' => $shipment->status,
            'status_before_action' => $shipment->status,
            'action' => 'update_phone',
        ]);

        return Redirect::route('shipments.show', $shipment)->with('success', trans('mobile_number_updated_seccessfully'));
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $this->authorize('isEmployeeHasPermission', 'status_shipments');

        if ($shipment->status == 100 || $shipment->status == -100)
            abort(403, trans('Completed_or_returned_shipments_cannot_be_modified'));

        $request->validate([
            'status' => 'required',
        ]);

        $status_before_action = $shipment->status;

        $update =   $shipment->update([
                'status' => intval($request->status),
            ]);

        if ( $update && $request->status == 100 && $shipment->jtexpress) {
            $shipment->jtexpress->update([
                'from_us'=>true,
            ]);
        }

        if (auth()->user()->type == "employee") {
            $shipment->update([
                'operator_id' => null,
                'employee_id' => auth()->user()->employee->id,
            ]);
        }

        if ($request->status == 100 || $request->status == -100 || $request->status == -1) {
            $fees = calculateFees($shipment->financial);

            //tax
            $tax = $fees - $fees / ((float)getSettingValue('vat') * 0.01 + 1);
            $shipment->financial()->update([
                'tax'            => $tax,
            ]);

            if ($request->status == 100) {
                $shipment->update([
                    'shipping_date' => now(),
                ]);
                if ($shipment->financial->wallet) {
                    $shipment->store()->increment('dues', $fees);
                    $shipment->store()->decrement('dues', $shipment->financial->cod);
                }
            }

            if ($request->status == -100 && $shipment->financial->wallet) {
                $shipment->store()->increment('dues', $fees);
            }
        }

        $shipment->shipmentTracks()->create([
            'user_id' => Auth::user()->id,
            'status_after_action' => $shipment->status,
            'status_before_action' => $status_before_action,
            'action' => 'update_status',
        ]);

        ParcelHelper::tracking($shipment);

        if ($shipment->salla_order_id)
            SallaHelper::updateShipmentStatus($shipment);

        if ($shipment->zid_order_id)
            ZidHelper::updateOrderStatus($shipment);


        return Redirect::route('shipments.edit', $shipment)->with('success', trans('shipment_status_updated_successfully'));
    }

    public function cancel(Request $request)
    {
        $this->authorize('isClient');

        $request->validate([
            'number' => 'required',
        ]);

        $shipment = Shipment::where('number', $request->number)->where('store_id', Auth()->user()->store->id)->first();

        if (!$shipment)
            return Redirect::back()->with('error', trans('Sorry_shipment_number_is_wrong'));

        if ($shipment->status != 10)
            return Redirect::back()->with('error', trans('Only_already_Created_shipments_can_be_canceled'));

        $status_before_action = $shipment->status;

        $update = $shipment->update([
            'status' => -1,
        ]);

        if ($update) {
            $fees = calculateFees($shipment->financial);
            $tax = $fees - $fees / ((float)getSettingValue('vat') * 0.01 + 1);

            $shipment->financial()->update([
                'tax'            => $tax,
            ]);

            $track = $shipment->shipmentTracks()->create([
                'user_id' => Auth::user()->id,
                'status_after_action' => $shipment->status,
                'status_before_action' => $status_before_action,
                'action' => 'update_status',
            ]);
            $toroodResponse = ParcelHelper::tracking($shipment);
            if ($shipment->salla_order_id)
                SallaHelper::updateShipmentStatus($shipment);

            if ($shipment->zid_order_id)
                ZidHelper::updateOrderStatus($shipment);
        }


        return Redirect::back()->with('success', trans('Shipment status updated successfully'));
    }

    public function downloadExcel()
    {
        $this->authorize('isEmployeeHasPermission', 'create_shipments');
        if (Auth::user()->type == 'client')
            return response()->download('import.xlsx');
        else
            return response()->download('import_shipments.xlsx');
    }

    public function proceedings()
    {
        return Inertia::render('Shipments/Proceedings', [
            'Operators' => Operator::whereHas('user', function ($query) {
                return $query->where('type', 'operator');
            })
                ->where('type', '!=', 'vip')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($operator) => [
                    'id' => $operator->id,
                    'username' => $operator->user->username,
                ]),
        ]);
    }







    public function file(Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'index_shipments');

        $request->validate([
            'shipments' => 'required',
        ]);

        if ($request->policies) {

            foreach ($request->shipments as $id) {
                $shipment = Shipment::find($id);
                $pdf = PDF::loadView('bill', compact('shipment'));
                Storage::put('public/folder/' . $shipment->number . '.pdf', $pdf->output());
            }

            $fileName = 'policies.zip';
        } else {

            foreach ($request->shipments as $id) {
                $shipment = Shipment::find($id);
                $shipment = [
                    'id' => $shipment->id,
                    'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
                    'date' =>  $shipment->updated_at->format('Y-m-d H:i:s'),
                    'number' => $shipment->number,
                    'status' => $shipment->status,
                    'storeCity' => $shipment->store->city,
                    'storeName' => $shipment->store->name,
                    'storeNeighborhood' => $shipment->store->neighborhood,
                    'storePhone' => $shipment->store->user->phone,
                    'storeTaxNumber' => $shipment->store->tax_number,
                    'details' => $shipment->details,
                    'receiverName' => $shipment->receiver_name,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'receiverPhone' => $shipment->receiver_phone,
                    'cod' => $shipment->financial->cod + 0,
                    'fees' => calculateFees($shipment->financial),
                    'tax' => $shipment->financial->tax,
                    'created_at' => $shipment->created_at->format('Y-m-d'),
                ];
                $pdf = PDF::loadView('pdf.invoice', compact('shipment'), [], ['format'  => 'A4']);
                Storage::put('public/folder/' . $shipment['number'] . '.pdf', $pdf->output());
            }

            $fileName = 'invoices.zip';
        }

        $zip = new ZipArchive;

        File::delete(Storage::path('public/zip/' . $fileName));

        if ($zip->open(Storage::path('public/zip/' . $fileName), ZipArchive::CREATE) === TRUE) {
            $files = File::files(Storage::path('public/folder'));

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();

            File::deleteDirectory(Storage::path('public/folder'));
        }



        return response()->download(Storage::path('public/zip/' . $fileName));
    }

    public function failedDelivery($id, Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'failed_shipments');

        $shipment = Shipment::findOrfail($id);
        $update  = $shipment->update([
            'failed' => $shipment->failed + 1,
        ]);

        if ($update) {
            $track = $shipment->shipmentTracks()->create([
                'user_id' => Auth::user()->id,
                'status_after_action' => $shipment->status,
                'status_before_action' => $shipment->status,
                'action' => 'failed_delivery',
                'notice' => $request->notice
            ]);
            return Redirect::back()->with('success', trans('shipment_status_canceled_successfully'));
        }
    }

    public function failedDeliveryShipments()
    {
        $this->authorize('isEmployeeHasPermission', 'failed_shipments');

        if (Gate::allows('isAdminOrEmployee'))
            return Inertia::render('Shipments/FailedDeliveryShipments', [
                'filters' => RequestFacade::all('search', 'status'),
                'shipments' => Shipment::with('financial')
                    ->where('failed', '>', 0)
                    ->orderBy('status', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->filter(RequestFacade::only('search', 'status'))
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'storeName' => $shipment->store->name,
                        'failed' => $shipment->failed,
                        'operator' => $shipment->operator->user->name ?? "-",

                    ]),
            ]);

        elseif (Gate::allows('isClient'))
            return Inertia::render('Shipments/FailedDeliveryShipments', [
                'filters' => RequestFacade::all('search', 'status'),
                'shipments' => Shipment::with('financial')
                    ->where('failed', '>', 0)
                    ->where('store_id', Auth::user()->store->id)
                    ->orderBy('status', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->filter(RequestFacade::only('search', 'status'))
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($shipment) => [
                        'id' => $shipment->id,
                        'number' => $shipment->number,
                        'receiverPhone' => $shipment->receiver_phone,
                        'receiverName' => $shipment->receiver_name,
                        'failed' => $shipment->failed,
                    ]),
            ]);
    }

    public function receive()
    {
        $this->authorize('isEmployeeHasPermission', 'receive_shipments');

        $stores = Store::whereHas('shipments', function ($query) {
            return $query->where('status', 10);
        })
        ->whereIn('type', ['shop', 'individual'])
        ->when(auth()->user()->type == 'operator', function ($query) {
            $query->where('operator_id', auth()->user()->operator->id);
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn ($store) => [
            'id' => $store->id,
            'name' => $store->name,
            'city' => $store->city,
            'neighborhood' => $store->neighborhood,
            'phone' => $store->user->phone,
            'operator' => $store->operator->user->name ?? null,
            'shipments' => $store->shipments()->where('status', 10)->count(),
            'showForm' => false
        ]);

        $addressesPlatforms = PickupAddress::whereHas('shipments', function ($query) {
            return $query->where('status', 10);
        })->whereHas('store', function ($query) {
            return $query->where('type', 'platform');
        })
        ->when(auth()->user()->type == 'operator', function ($query) {
            $query->where('operator_id', auth()->user()->operator->id);
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn ($address) => [
            'id' => $address->store->id,
            'platformId' => $address->id,
            'platformName' => $address->store->name,
            'name' => $address->name,
            'city' => $address->city,
            'neighborhood' => $address->neighborhood,
            'phone' => $address->phone,
            'operator' => $address->operator->user->name ?? null,
            'shipments' => $address->shipments()->where('status', 10)->count(),
            'showForm' => false
        ]);


        $merged = $stores->toBase()->merge($addressesPlatforms)->sortBy('id');


        return Inertia::render('Shipments/Receive', [
            'filters' => RequestFacade::all('search'),
            'stores'  => $merged,
        ]);
    }

    public function receivePdf()
    {
        $this->authorize('isEmployeeHasPermission', 'receive_shipments');


        $stores = Store::whereHas('shipments', function ($query) {
            return $query->where('status', 10);
        })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($store) => [
                'id' => $store->id,
                'name' => $store->name,
                'city' => $store->city,
                'neighborhood' => $store->neighborhood,
                'phone' => $store->user->phone,
                'shipments' => $store->shipments()->where('status', 10)->count(),

            ]);

        $pdf = PDF::loadView('pdf.receive', compact('stores'), [], ['format'  => 'A4']);
        return $pdf->download('receive.pdf');
    }

    public function invoicePdf($slug)
    {
        $shipment = Shipment::where('number', $slug)->firstOrFail();
        if (!in_array($shipment->status, [100, -100, -1]))
            abort(404);

        $shipment = [
            'id' => $shipment->id,
            'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
            'date' =>  $shipment->updated_at->format('Y-m-d H:i:s'),
            'number' => $shipment->number,
            'status' => $shipment->status,
            'storeCity' => $shipment->store->city,
            'storeName' => $shipment->store->name,
            'storeNeighborhood' => $shipment->store->neighborhood,
            'storePhone' => $shipment->store->user->phone,
            'storeTaxNumber' => $shipment->store->tax_number,
            'details' => $shipment->details,
            'receiverName' => $shipment->receiver_name,
            'city' => $shipment->city,
            'neighborhood' => $shipment->neighborhood,
            'receiverPhone' => $shipment->receiver_phone,
            'cod' => $shipment->financial->cod + 0,
            'fees' => calculateFees($shipment->financial),
            'tax' => $shipment->financial->tax,
            'created_at' => $shipment->created_at->format('Y-m-d'),
        ];

        $pdf = PDF::loadView('pdf.invoice', compact('shipment'), [], ['format'  => 'A4']);
        return $pdf->stream('SA' . $shipment['invoice'] . '.pdf');
    }
}