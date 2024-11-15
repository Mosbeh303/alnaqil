<?php

namespace App\Http\Controllers;

use App\Models\PickupAddress;
use App\Models\Store;
use Illuminate\Http\Request;

class PickupAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required',
            'neighborhood' => 'required|string|max:255',
            'phone' => 'numeric|nullable',
        ],
        [
            'required' => trans('required_field')
        ]);



        if ($request->default){
            $store->pickupAddresses()->update(['default' => 0]);
        }

        $store->pickupAddresses()->create([
            'name' => $request->name,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'national_address' => $request->nationalAddress,
            'full_address' => $request->fullAddress,
            'default' => $request->default,
            'phone' => $request->phone
        ]);



        return back()->with('success', trans('added_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PickupAddress  $pickupAddress
     * @return \Illuminate\Http\Response
     */
    public function show(PickupAddress $pickupAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PickupAddress  $pickupAddress
     * @return \Illuminate\Http\Response
     */
    public function edit(PickupAddress $pickupAddress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PickupAddress  $pickupAddress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PickupAddress $pickupAddress)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required',
            'neighborhood' => 'required|string|max:255',
            'phone' => 'numeric|nullable',
        ],
        [
            'required' => trans('required_field')
        ]);

        if ($request->default){
           PickupAddress::where('store_id', $pickupAddress->store_id)->update(['default' => 0]);
        }

        $pickupAddress->update([
            'name' => $request->name,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'national_address' => $request->nationalAddress,
            'full_address' => $request->fullAddress,
            'default' => $request->default,
            'phone' => $request->phone
        ]);

        return back()->with('success', trans('updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PickupAddress  $pickupAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(PickupAddress $pickupAddress)
    {
        $pickupAddress->delete();
        return back()->with('success', trans('address_deleted_successfully'));

    }
}
