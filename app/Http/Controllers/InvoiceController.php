<?php

namespace App\Http\Controllers;

use App\Models\Financial;
use Illuminate\Http\Request;
use Inertia\Inertia ;
use App\Models\Invoice;
use App\Models\Shipment;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\invoicesExport;

class InvoiceController extends Controller
{
    public function index(Request $request){
        $this->authorize('isEmployeeHasPermission', 'invoices_stores');

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'nullable|exists:stores,id',
            ]);
                $store = Store::find($request->store);

        }


        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        //$user = auth()->user();
        return Inertia::render('Invoices/Index', [
            'invoices' => Invoice::with('store')
            ->when($store, function ($query) use ($store) {
                $query->where('store_id', $store->id);
                })
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(50)
            ->withQueryString()
            ->through(fn ($invoice) => [
                'id' => $invoice->id,
                'storeNumber' => $invoice->store->id,
                'storeName' => $invoice->store->name,
                'number' => $invoice->created_at->format('Y') . '-' . str_pad($invoice->number, 5, '0', STR_PAD_LEFT),
                'date' => $invoice->created_at->format('Y-m-d'),
                'fees_without_tax' => $invoice->fees() -  ($invoice->fees() - $invoice->fees() / ((float)getSettingValue('vat') * 0.01 + 1)),
                'tax' => $invoice->fees() - $invoice->fees() / ((float)getSettingValue('vat') * 0.01 + 1),
                'fees' => $invoice->fees(),
                'tax_number' => $invoice->store->tax_number
            ]),
            'stores' => Store::has('invoices')->get()
            ->map(function ($store) {
                return [
                    'value' => $store->id,
                    'label' => $store->id . '- ' . $store->name,
                ];
            }),

            'fromDate' => $request->from,
            'to'   => $request->to,
            'store'   => $request->store
        ]);
    }






    public function invoiceExel(Request $request)
    {


          //   $this->authorize('isEmployeeHasPermission', 'invoice_stores');

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'nullable|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }

        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $items = Invoice::with('store')
        ->when($store, function ($query) use ($store) {
            $query->where('store_id', $store->id);
        })
        ->when($from && $to, function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        })

        ->when(!$from && !$to && !$store, function ($query) {
            return $query;            })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn ($invoice) => [
            'id' => $invoice->id,
            'storeNumber' => $invoice->store->id,
            'storeName' => $invoice->store->name,
            'number' => $invoice->created_at->format('Y') . '-' . str_pad($invoice->number, 5, '0', STR_PAD_LEFT),
            'date' => $invoice->created_at->format('Y-m-d'),
            'fees_without_tax' => $invoice->fees - $invoice->tax,
            'tax' => $invoice->tax,
            'fees' => $invoice->fees,
            'tax_number' => $invoice->store->tax_number
        ]);








        return Excel::download(new invoicesExport($items), 'invoices.xlsx');

    }




    public function invoicePdf(Request $request)
    {
     //   $this->authorize('isEmployeeHasPermission', 'invoice_stores');

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'nullable|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }

        $from = null ;
        $to = null ;

        if ($request->from)
            $from = date('Y-m-d', strtotime($request->from));

        if ($request->to)
            $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $items = Invoice::with('store')
            ->when($store, function ($query) use ($store) {
                $query->where('store_id', [$store->id]);
            })
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })

            ->when(!$from && !$to && !$store, function ($query) {
                return $query;            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($invoice) => [
                'id' => $invoice->id,
                'storeNumber' => $invoice->store->id,
                'storeName' => $invoice->store->name,
                'number' => $invoice->created_at->format('Y') . '-' . str_pad($invoice->number, 5, '0', STR_PAD_LEFT),
                'date' => $invoice->created_at->format('Y-m-d'),
                'fees_without_tax' => $invoice->fees - $invoice->tax,
                'tax' => $invoice->tax,
                'fees' => $invoice->fees,
                'tax_number' => $invoice->store->tax_number
        ]);


        $to = date('Y-m-d', strtotime($request->to ));

        if ($store){
            $store = $store->name ;
        } else {
            $store = null ;
        }

        $pdf = PDF::loadView('pdf/invoices', compact('items', 'from' , 'to', 'store'), [], ['format'  => 'A4', 'mode' => 'landscape']);
        return $pdf->download('invoices.pdf');
    }

    public function create (){

        $this->authorize('isEmployeeHasPermission', 'invoices_stores');

        return Inertia::render('Invoices/Create', [
            'stores' => Store::where('auto_billing', 0)
            ->get()
                ->map(fn ($store) => [
                    'value' => $store->id,
                    'label'  => $store->id . '- ' . $store->name,
                ]),
            'max_date' => date("Y-m-d", strtotime("yesterday")),
        ]);
    }

    public function dateRange (Request $request){

       $store = Store::find($request->store);


       return [
        'max_date' => date("Y-m-d", strtotime("yesterday")),
        'min_date' => $store->invoices()->exists() ? date("Y-m-d", strtotime($store->invoices()->latest()->first()->end_date  . "+1 day" )) : date("Y-m-d", strtotime( "2023-09-09"))
       ];
    }

    public function preview (Request $request){
        $this->authorize('isEmployeeHasPermission', 'invoice_stores');



        $request->validate([
            'store' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }


        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        //dd($to);

        $items = $store->shipments->sortBy('updated_at')->values()->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to])->map(fn ($shipment) => [
            'id' => $shipment->id,
            'number' => $shipment->number,
            'shipping_date' => $shipment->updated_at->format('Y-m-d'),
            'fees' => calculateFees($shipment->financial),
            'cod' => $shipment->financial->cod + 0,
            'receiverPhone' => $shipment->receiver_phone,
            'status' => $shipment->status,
            'tax' => $shipment->financial->tax,
        ]) ;

        $sum = [
            'fees' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->where('ADP', null)->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')) +
            Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->where('ADP', '!=' ,null)->sum('ADP') ,
            'tax' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum('tax'),
            'cod' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100])->whereBetween('updated_at', [$from, $to]);
            })->sum('cod'),
        ];

        // $to = $request->to;
        // $from = $request->from;
        $date = date('Y-m-d');

        //dd($sum);



        $pdf = PDF::loadView('pdf/store-invoice', compact('items', 'sum', 'date', 'store'), [], ['format'  => 'A4']);
        return $pdf->stream('invoice-preview.pdf');
    }

    public function show (Invoice $invoice){
        $this->authorize('isEmployeeHasPermission', 'invoices_stores');


        $store = $invoice->store;

        if (Auth::user()->type == "client" && $invoice->store_id != Auth::user()->store->id)
            abort(401);

        $items = $invoice->financials()->with('shipment')->get()->map(fn ($financial) => [
            'id' => $financial->shipment->id,
            'number' => $financial->shipment->number,
            'shipping_date' => $financial->shipment->updated_at->format('Y-m-d'),
            'fees' => calculateFees($financial),
            'cod' => $financial->cod + 0,
            'receiverPhone' => $financial->shipment->receiver_phone,
            'status' => $financial->shipment->status,
            'tax' => $financial->tax,
        ]) ;

        $sum = [
            'fees' => $invoice->fees(),
            'tax' => $invoice->financials()->sum('tax'),
            'cod' => $invoice->financials()->whereHas('shipment', function ($query){
                $query->where('status', 100);
            })->sum('cod'),
        ];

        $date = date('Y-m-d', strtotime($invoice->created_at ));
        $number = $invoice->created_at->format('Y') . '-' . str_pad($invoice->number, 5, '0', STR_PAD_LEFT);

        $pdf = PDF::loadView('pdf/store-invoice', compact('items', 'sum', 'date', 'store', 'number'), [], ['format'  => 'A4']);
        return $pdf->stream('invoice-preview.pdf');
    }

    public function store (Request $request){
        $this->authorize('isEmployeeHasPermission', 'invoices_stores');



        $request->validate([
            'store' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }


        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        //dd($from);

        $number = DB::table('invoices')->whereYear('created_at', '=', date("Y"))->max('number') + 1;

        $invoice = $store->invoices()->create([
            'number' => $number,
            'user_id' => Auth::user()->id,
            'start_date' => $from,
            'end_date' => date('Y-m-d', strtotime($request->to)),
            'fees' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')),
            'tax' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum('tax'),
            'cod' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100])->whereBetween('updated_at', [$from, $to]);
            })->sum('cod'),
        ]);

        Financial::whereHas('shipment', function ($query) use ($store , $from, $to) {
            $query->whereIn('status', [100, -100])
                ->where('store_id', $store->id)
                ->whereBetween('updated_at', [$from, $to]);
            })->update(['invoice_id' => $invoice->id]);






        // $to = $request->to;
        // $from = $request->from;
       // $to = date('Y-m-d', strtotime($request->to ));

       // $pdf = PDF::loadView('pdf/store-invoice', compact('items', 'sum', 'to', 'from', 'store'), [], ['format'  => 'A4']);
       return Redirect::route('invoices')->with('success', trans('created_successfully'));

    }



}