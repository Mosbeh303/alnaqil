<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Shipment;
use App\Models\ShipmentTrack;
use App\Models\Store;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use ZipArchive;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ShipmentTrackController extends Controller
{
    protected $actions;
    protected $statuses;
    protected $descriptions;
    protected $fields;
    public function __construct()
    {
        $this->actions = [
            'create' => trans('shirpmet_created_successfully'),
            'recieve' => trans('shipment_from_store_received'),
            'processing' => trans('shipment_received_and_in_processed'),
            'assign' => trans("shipment_received_from_warehouse"),
            'completed' => trans('shipment_delivered'),
            'returned' => trans('Shipment_returned'),
            'cancelled' => trans('Shipment_cancelled'),

            'update' => trans('shipment_updated_successfully'),
            'update_status' => trans('shipment_status_updated_successfully'),
            'update_warehouse' => trans('Shipment_storage_location_updated'),
            'update_phone' => trans('recipient_mobile_number_updated'),
            'failed_delivery' => trans('unsuccessful_delivery_attempt'),
            'update_operator' => trans('attach_a_representative_to_shipment'),
        ];

        $this->statuses = [
            '10' => 'create',
            '20' => 'recieve',
            '35' => 'processing',
            '65' => 'assign',
            '100' => 'completed',
            '-100' => 'returned',
            '-1' => 'cancelled',
        ];

        $this->descriptions = [
            'create' => trans('shipment_created_by'),
            'recieve' => trans('shipment_received_from_the_store_by'),
            'processing' => trans('shipment_being_processed_by'),
            'assign' => trans('shipment_received_from_the_warehouse_by'),
            'completed' => trans('shipment_delivered_by'),
            'returned' => trans('shipment_was_returned_by'),
            'cancelled' => trans('Shipment_canceled_by'),

            'update' => trans('Shipment_modified_by'),
            'update_status' => trans('shipment_status_modified_by'),
            'update_warehouse' => trans('shipment_storage_location_modified_by') ,
            'update_phone' => trans('recipient_mobile_number_modified_by'),
            'failed_delivery' => trans('Unsuccessful_delivery_attempt_by'),
            'update_operator' => trans('delegate_attached'),
        ];
        $this->fields = [
            'details' => trans('package_contents'),
            'base_fee' => trans('shipping_fee'),
            'receiver_name' => trans('The_recipients_name'),

            'city' => trans('city_'),
            'street' => trans('street_name_'),
            'cod' => trans('shipment_amount'),
            'weight' => trans('package_weight'),
            'order_id' => trans('order_number'),
            'store_id' => trans('store_number'),

            'neighborhood' => trans('Neighborhood'),
            'receiver_phone' => trans('recipients_mobile_number'),
            'number_of_pieces' => trans('the_number_of_pieces'),

            'exchange_fee' => trans('replacement_from_the_customer'),
            'extra_fees' => trans('retrieval_from_the_client'),
            'refrigerated_transport_fee' =>trans('refrigerated_transport'),
        ];
    }

    public function tracking(Request $request)
    {
        return Inertia::render('Shipments/Tracking');
    }

    public function getTracking(Request $request)
    {
        $request->validate([
            'all' => 'required',
        ]);

        //$searchArray = explode(" ", $request->all);

        return Inertia::render('Shipments/Tracking', [
            'shipments' => Shipment::with('financial')
            // ->orderByName()
                ->whereIn('number', $request->all)
                ->orWhereIn('receiver_phone', $request->all)
                ->paginate(10)
                ->withQueryString()
                ->through(fn($shipment) => [
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
                ]),
        ]);
    }

    public function getShipmentTracking($number)
    {

        return Inertia::render('Shipments/ShipmentTracking', [
            'tracks' => ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
                return $query->where('number', $number);
            })->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($track) => [
                    'id' => $track->id,
                    'number' => $track->shipment->number,
                    'created_at' => $track->created_at->format('Y-m-d H:i:s'),
                    'userFullName' => $track->user->type === 'client' ? $track->user->store->name : $track->user->name . ' ' . $track->user->lastname,
                    'username' => $track->user->username,
                    'action' => $track->action,
                    'operator' => $track->operator_id ? $track->operator->user->name : null,
                    'status' => $track->status_after_action,
                    'original' => json_decode($track->original),
                    'changes' => json_decode($track->changes),
                ]),
            'action' => $this->actions,
            'description' => $this->descriptions,
            'number' => $number,
            'statuses' => $this->statuses,
            'fields' => $this->fields,
        ]);
    }

    public function getShipmentTrackingPdf($number)
    {

        $tracks = ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
            return $query->where('number', $number);
        })->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($track) => [
                'id' => $track->id,
                'number' => $track->shipment->number,
                'created_at' => $track->created_at->format('Y-m-d H:i:s'),
                'userFullName' => $track->user->type === 'client' ? $track->user->store->name : $track->user->name . ' ' . $track->user->lastname,
                'username' => $track->user->username,
                'action' => $track->action,
                'status' => $track->shipment->status,
            ]);

        $action = $this->actions;
        $description = $this->descriptions;

        $pdf = PDF::loadView('pdf/tracking', compact('tracks', 'action', 'description', 'number'), [], ['format' => 'A4', 'mode' => 'landscape']);
        return $pdf->download($number . '.pdf');
    }

    public function shipmentsTrackingPdf(Request $request)
    {
        //dd($request->shipments);

        $request->validate([
            'all' => 'required',
        ]);

        $shipmentsNumbers = explode(",", $request->all);

        foreach ($shipmentsNumbers as $number) {
            $tracks = ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
                return $query->where('number', $number);
            })->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($track) => [
                    'id' => $track->id,
                    'number' => $track->shipment->number,
                    'created_at' => $track->created_at->format('Y-m-d H:i:s'),
                    'userFullName' => $track->user->name . ' ' . $track->user->lastname,
                    'username' => $track->user->username,
                    'action' => $track->action,
                    'status' => $track->shipment->status,
                ]);

            if ($tracks->isEmpty()) {
                continue;
            }

            $action = $this->actions;
            $description = $this->descriptions;

            $pdf = PDF::loadView('pdf/tracking', compact('tracks', 'action', 'description', 'number'), [], ['format' => 'A4', 'mode' => 'landscape']);
            Storage::put('public/tracking/' . $number . '.pdf', $pdf->output());
        }

        $zip = new ZipArchive;

        $fileName = 'tracking.zip';

        File::delete(Storage::path('public/zip/' . $fileName));

        if ($zip->open(Storage::path('public/zip/' . $fileName), ZipArchive::CREATE) === true) {
            $files = File::files(Storage::path('public/tracking'));

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();

            File::deleteDirectory(Storage::path('public/tracking'));
        }

        return response()->download(Storage::path('public/zip/' . $fileName));

    }

    public function clientTracking(Request $request)
    {
        $request->validate([
            'number' => 'required|exists:App\Models\Shipment,number',
        ],
            [
                'numbers.exists' => trans('shipment_number_does_not_exist'),
            ]);

        $number = $request->number;

        $shipment = Shipment::where('number', $number)->first();

        $tracks = ShipmentTrack::whereHas('shipment', function ($query) use ($number) {
            return $query->where('number', $number);
        })->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($track) => [
                'id' => $track->id,
                'number' => $track->shipment->number,
                'created_at' => $track->created_at->format('Y-m-d H:i:s'),
                'userFullName' => $track->user->name . ' ' . $track->user->lastname,
                'username' => $track->user->username,
                'action' => $track->action,
                'notice' => $track->notice,
                'status' => $track->shipment->status,
                'status_after_action' => $track->status_after_action,
            ]);

        $action = $this->actions;
        $statuses = $this->statuses;

        return view('front.tracking', compact('tracks', 'shipment', 'action', 'statuses'));
    }

}
