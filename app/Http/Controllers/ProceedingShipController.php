<?php

namespace App\Http\Controllers;

use App\Integrations\Jtexpress\JtexpressHelper;
use App\Models\Store ;
use App\Models\Shipment ;
use App\Models\ShipmentTrack ;
use App\Models\Operator ;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS RequestFacade;
use Meneses\LaravelMpdf\Facades\LaravelMpdf AS PDF  ;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Gate;
use Auth ;
use Illuminate\Support\Facades\Storage;
use ZipArchive ;
use File;
use DB;
use App\Integrations\Torood\ParcelHelper;
use App\Integrations\Salla\SallaHelper;
use App\Integrations\Zid\ZidHelper;
use App\Rules\OperatorPhoneIdRule;

class ProceedingShipController extends Controller
{
    public function getProceedingShipments(Request $request)
    {
        //$this->authorize('isEmployeeHasPermission', 'query_shipments');

        $request->validate([
            'all' => 'required',
        ]);

        //$searchArray = explode(" ", $request->all);

        return Inertia::render('Shipments/Proceedings', [
            'shipments' => Shipment::with('financial')
               // ->orderByName()
                ->whereIn('number', $request->all)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                     'operator'   => $shipment->operator ? [
                        'id' => $shipment->operator->id ,
                        'name' => $shipment->operator->user->name,
                     ] : null ,
                     'employee'   => $shipment->employee ? [
                        'id' => $shipment->employee->id ,
                        'name' => $shipment->employee->user->name,
                     ] : null ,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'warehouseLocation' => $shipment->warehouse_location,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                    'branchForEmployee' => $shipment->employee->branch ?? null,
                    'branchForOperator' => $shipment->operator->branch ?? null,
                    'exchange' => $shipment->exchange,
                    'refrigerated' => $shipment->refrigerated,
                    'return_back' => $shipment->return_back,
                ]),

            'Operators' => Operator::whereHas('user', function ($query){
                return $query->where('type', 'operator');
                })
                ->where('type','!=', 'vip')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($operator) => [
                    'id' => $operator->id,
                    'username' => $operator->user->username,
                    'name'     => $operator->user->name,
                ]),
        ]);
    }

    public function action ( Request $request, $action ){

        $request->validate([
            'all' => 'required',
        ],
        [
            'all.required' => trans('no_shipments')
        ]);

        //dd($request->all);

        if (!is_array($request->all))
           $numbers = array_unique(explode(',', $request->all)) ;
        else
            $numbers = array_unique($request->all);

        if ($action == 'status'){

            $request->validate([
                'status' => 'required',
            ]);



            foreach($numbers as $number){
                $shipment = Shipment::where('number', $number)->first();
                if($shipment){
                    $status_before_action = $shipment->status;
                    $update = $shipment->update([
                        'status' => $request->status
                    ]);

                    if ($request->status == 100 || $request->status == -100){
                        $fees = calculateFees($shipment->financial) ;

                        //tax
                        $tax = $fees - $fees/((float)getSettingValue('vat') * 0.01 + 1) ;
                        $shipment->financial()->update([
                            'tax'            => $tax ,
                        ]);

                        if ($request->status == 100){
                            $shipment->update([
                                'shipping_date' => now(),
                            ]);

                            if($shipment->financial->wallet){
                                $shipment->store()->increment('dues', $fees);
                                $shipment->store()->decrement('dues', $shipment->financial->cod);
                            }
                        }

                        if (($request->status == -100) && $shipment->financial->wallet){
                            $shipment->store()->increment('dues', $fees);
                        }

                    }

                    if ($update){

                        if ( $request->status == 100 && $shipment->jtexpress) {
                            $shipment->jtexpress->update([
                                'from_us' => true,
                            ]);
                        }

                        $shipment->shipmentTracks()->create([
                            'user_id' => Auth::user()->id,
                            'status_after_action' => $shipment->status    ,
                            'status_before_action' => $status_before_action   ,
                            'action' => 'update_status'  ,
                        ]);

                        ParcelHelper::tracking($shipment);

                        if ($shipment->salla_order_id)
                            SallaHelper::updateShipmentStatus($shipment);

                        if ($shipment->zid_order_id)
                            ZidHelper::updateOrderStatus($shipment);

                        if (auth()->user()->type == "employee"){
                            $shipment->update([
                                'operator_id' => null,
                                'employee_id' => auth()->user()->employee->id,
                            ]);
                        }

                    }

                }

            }
        }

        if($action == "warehouse") {

            $request->validate([
                'location' => 'required',
            ]);


            foreach($numbers as $number){
                $shipment = Shipment::where('number', $number)->first();
                if($shipment){
                    $shipment->update([
                        'warehouse_location' => $request->location
                    ]);

                    $track = $shipment->shipmentTracks()->create([
                        'user_id' => Auth::user()->id,
                        'status_after_action' => $shipment->status    ,
                        'status_before_action' => $shipment->status    ,
                        'action' => 'update_warehouse'  ,
                    ]);
                }
            }
        }

        if($action == "operator") {

            $request->validate([
                'operator' => ['required', new OperatorPhoneIdRule],
            ]);




            $operator = Operator::WhereHas('user', function ($query) use ($request) {
                $query->where('phone', $request->operator);
            })->orWhere('identification', $request->operator)->first();

            foreach($numbers as $number){
                $shipment = Shipment::where('number', $number)->first();
                if($shipment){
                    $shipment->update([
                        'operator_id' => $operator->id,
                        'employee_id' => null,
                    ]);

                    $track = $shipment->shipmentTracks()->create([
                        'user_id' => Auth::user()->id,
                        'operator_id' => $operator->id,
                        'status_after_action' => $shipment->status    ,
                        'status_before_action' => $shipment->status   ,
                        'action' => 'update_operator'  ,
                    ]);
                }
            }
        }

        if($action == "jt") {

            foreach($numbers as $number){
                $shipment = Shipment::where('number', $number)->first();
                if ($shipment->jtexpress->bill_code ?? null ){
                    $jt = json_decode(JtexpressHelper::printOrder($shipment->jtexpress->bill_code));
                    if ($jt->code == 1){
                        $pdf = file_get_contents($jt->data);
                        Storage::put('public/folder/'.$shipment->number.'.pdf', $pdf);
                    }
                }
            }

            if(Storage::exists('public/folder')){
                $fileName = 'jt-waybills.zip';

                $zip = new ZipArchive;

                File::delete(Storage::path('public/zip/'.$fileName));

                if ($zip->open(Storage::path('public/zip/'.$fileName), ZipArchive::CREATE) === TRUE)
                {
                    $files = File::files(Storage::path('public/folder'));

                    foreach ($files as $key => $value) {
                        $relativeNameInZipFile = basename($value);
                        $zip->addFile($value, $relativeNameInZipFile);
                    }

                    $zip->close();

                    File::deleteDirectory(Storage::path('public/folder'));
                }

                return response()->download(Storage::path('public/zip/'.$fileName));
            }
        }

        //dd($numbers);

        return Inertia::render('Shipments/Proceedings', [
            'shipments' => Shipment::with('financial')
               // ->orderByName()
                ->whereIn('number', $numbers)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'operator'   => $shipment->operator ? [
                        'id' => $shipment->operator->id ,
                        'name' => $shipment->operator->user->name
                     ] : null ,
                     'employee'   => $shipment->employee ? [
                        'id' => $shipment->employee->id ,
                        'name' => $shipment->employee->user->name,
                     ] : null ,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'warehouseLocation' => $shipment->warehouse_location,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                    'branchForEmployee' => $shipment->employee->branch ?? null,
                    'branchForOperator' => $shipment->operator->branch ?? null,
                    'exchange' => $shipment->exchange,
                    'refrigerated' => $shipment->refrigerated,
                    'return_back' => $shipment->return_back,
                ]),

            'Operators' => Operator::whereHas('user', function ($query){
                return $query->where('type', 'operator');
                })
                ->where('type','!=', 'vip')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($operator) => [
                    'id' => $operator->id,
                    'username' => $operator->user->username,
                    'name'     => $operator->user->name,
                ]),
        ]);
    }
}