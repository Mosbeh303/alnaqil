<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Receiver;
use App\Models\Financial;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Redirect;

class ReceiverController extends Controller
{
    public function index()
    {
        $this->authorize('isEmployeeHasPermission', 'receivers_stores');
        $receivers = Receiver::withCount('shipments')->get();

        return Inertia::render('Stores/Receivers/index', [
            'filters' => RequestFacade::all('search', 'status'),
            'receivers' => Receiver::orderBy('id', 'desc')
                ->whereHas('shipments')
                ->filter(RequestFacade::only('search'))
                ->paginate(50)
                ->withQueryString()
                ->through(fn($receiver) => [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'phone' => $receiver->phone,
                    'city' => $receiver->city,
                    'street' => $receiver->street,
                    'neighborhood' => $receiver->neighborhood,
                    'shipments' => $receiver->shipments->count(),
                    'id_number' => $receiver->id_number,
                    'national_adress' =>$receiver->national_adress,
                    'door_number' => $receiver->door_number,
                    // 'location_data' => $receiver->neighborhood,
                    'location_adress' => $receiver->location_adress,
                    'link' => url('receivers/' . base64_encode($receiver->phone) . '/create-additional-data'),

                ]),
        ]);
    }

    public function create($receiverPhone)
    {



        $receiver = Receiver::where('phone', $receiverPhone)->first();

        return Inertia::render('Stores/Receivers/additionalData', compact('receiver'));

    }

    public function show(Receiver $receiver)
    {
        $this->authorize('isEmployeeHasPermission', 'receivers_stores');
        $shipmentsNumber = $receiver->shipments()->count();
        return Inertia::render('Stores/Receivers/show', compact('receiver', 'shipmentsNumber'));
    }


    public function receiverData($receiver_phone)
    {
        $receiver = Receiver::where('phone', $receiver_phone)->first();
        $lastShipmentContent = null;

        if ($receiver) {
            $lastShipmentContent = $receiver->shipments()
                ->latest()
                ->pluck('details')
                ->first();

        }

        return response()->json([
            'receiver' => $receiver,
            'lastShipmentContent' => $lastShipmentContent,

        ]);
    }

    public function edit(Receiver $receiver)
    {
        $this->authorize('isEmployeeHasPermission', 'receivers_stores');
        $cities = City::where('type', 'shipping_cities')->where('active', 1)->get() ?? [trans('alriyadh')];
        return Inertia::render('Stores/Receivers/edit', compact('receiver', 'cities'));
    }

    public function updateData(Request $request, Receiver $receiver)
    {
        $this->authorize('isEmployeeHasPermission', 'receivers_stores');
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'digits:10'],

        ]);

        $updatedReceiverData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'id_number' => $request->id_number,

        ];

        $receiver->update($updatedReceiverData);

        return back()->with('success', trans('receiver_updated_successfully'));
    }





    public function update_door_photo(Request $request, Receiver $receiver)
    {
        $this->authorize('isEmployeeHasPermission', 'receivers_stores');

        $fileName = null;

        if ($request->file('door_photo')) {
            $fileName = time() . '.' . $request->file('door_photo')->extension();
            $request->file('door_photo')->move(public_path('uploads'), $fileName);

            $filePath = public_path('uploads/' . $receiver->door_photo);

            if (File::exists($filePath)) {

                File::delete($filePath);
            }
        }
        $updatedReceiverData = [

            'door_photo' => $fileName,

        ];

        $receiver->update($updatedReceiverData);

        return back()->with('success', trans('receiver_updated_successfully'));

    }






    public function updateCoordinates(Request $request, Receiver $receiver)
    {

        $request->validate([
            'national_adress' => ['nullable', 'string', 'regex:/^[a-zA-Z]{4}[0-9]{4}$/'],
        ]);

        $nat_address = null;
        $regex = '/([A-Za-z]{4}\d{4})/'; // Regular expression to match 4 characters followed by 4 digits
        $match = [];

        if (preg_match($regex,$request->national_adress, $match) && isset($match[1])) {
            $nat_address =  $match[1];
        }
        else {
       $nat_address = $request->national_adress ;
        }





        $this->authorize('isEmployeeHasPermission', 'receivers_stores');
        $userType = Auth::user()->type;

        if ($userType !== 'admin') {

            $request->validate([

                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'location' => 'required|string|max:255',

            ]);
        }




        $updatedReceiverData = [
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'street' => $request->street,
            'door_number' => $request->door_number,
            'location_data' => json_encode([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]),
            'location_adress' => $request->location,
            'national_adress' =>$nat_address,


        ];

        $receiver->update($updatedReceiverData);

        return back()->with('success', trans('receiver_updated_successfully'))->with('receiver', $receiver);
    }

    public function additionalData(Request $request, Receiver $receiver)
    {
        $request->validate([
            // 'id_number' => 'required|string|max:255',
            //'door_number' => 'required|string|max:255',
            //'street_name' => 'required|string|max:255',
            //'door_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location' => 'required|string|max:255',
            'national_adress' => ['nullable', 'string', 'regex:/^[a-zA-Z]{4}[0-9]{4}$/'],
        ]);





    $nat_address = null;
    $regex = '/([A-Za-z]{4}\d{4})/'; // Regular expression to match 4 characters followed by 4 digits
    $match = [];

    if (preg_match($regex,$request->national_adress, $match) && isset($match[1])) {
        $nat_address =  $match[1];
    }
    else {
   $nat_address = $request->national_adress ;
    }

        $fileName = null;

        if ($request->file('door_photo')) {
            $fileName = time() . '.' . $request->file('door_photo')->extension();
            $request->file('door_photo')->move(public_path('uploads'), $fileName);
        }


        $addedReceiverData = [
            'door_photo' => $fileName,

            'id_number' => $request->id_number,
            'id_number' => $request->id_number,
            'national_adress' =>$nat_address,
            'door_number' => $request->door_number,

            'street' => $request->street_name,
            'location_data' => json_encode([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]),
            'location_adress' => $request->location,
        ];

        $receiver->update($addedReceiverData);

        return back()->with('success', trans('additional_data_saved'));
    }

    public function shipments(Receiver $receiver, Request $request)
    {
        return Inertia::render('Shipments', [
            'receiver' => [
                'id' => $receiver->id,
                'name' => $receiver->name,
            ],

            'filters' => RequestFacade::all('search', 'status'),
            'shipments' => $receiver->shipments()
                ->when($request->search ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('number', 'like', '%' . $search . '%')
                            ->orWhere('city', 'like', '%' . $search . '%')
                            ->orWhere('receiver_phone', 'like', '%' . $search . '%')
                            ->orWhere('warehouse_location', 'like', '%' . $search . '%')
                            ->orWhereHas('store', function ($query) use ($search) {
                                $query->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })->when($request->status ?? null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'shipping_date' => $shipment->shipping_date,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'receiverName' => $shipment->receiver_name,
                    'receiverPhone' => $shipment->receiver_phone,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : '',
                ]),
        ]);
    }

    public function destroy(Receiver $receiver)
    {

        $receiver->delete();
        return redirect::back()->with('success', trans('receiver_deleted_successfully'));
    }
}
