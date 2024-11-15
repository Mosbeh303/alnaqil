<?php

namespace App\Http\Controllers\API\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\PickupAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{

    public function storeAdresses(Request $request)
    {
        try {


            Validator::make($request->all(),
            [
                [
                    'name' => 'required|string|max:255',
                    'city' => 'required',
                    'neighborhood' => 'required|string|max:255',
                    'phone' => 'numeric|nullable',
                ],
                [
                    'required' => trans('required_field')
                ]
            ]);



            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            if ($request->default) {
                $store->pickupAddresses()->update(['default' => 0]);
            }

            $store->pickupAddresses()->create([
                'name' => $request->name,
                'city' => $request->city,
                'neighborhood' => $request->neighborhood,
                'national_address' => $request->national_address,
                'full_address' => $request->full_address,
                'default' => $request->default,
                'phone' => $request->phone
            ]);

            $response = [
                'status' => true,
                'message' => trans('added_successfully'),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }


    public function updateAdresses(Request $request, PickupAddress $pickupAddress)
    {
        try {
            Validator::make($request->all(),
            [
                [
                    'name' => 'required|string|max:255',
                    'city' => 'required',
                    'neighborhood' => 'required|string|max:255',
                    'phone' => 'numeric|nullable',
                ],
                [
                    'required' => trans('required_field')
                ]
            ]);

            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();
            if($request->default){
                PickupAddress::where('store_id', $store->id)->update(['default' => 0]);
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

            $response = [
                'status' => true,
                'message' => trans('updated_successfully'),
                'data' =>  $store->pickupAddresses()->get(),

            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }
    public function deleteAdresses( PickupAddress $pickupAddress)
    {
        try {


            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

             $pickupAddress->delete();

            $response = [
                'status' => true,
                'message' => trans('deleted_successfully'),
                'data' =>  $store->pickupAddresses()->get(),

            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }


    public function getAddresses()
    {
        try {
            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            $response = [
                'status' => true,
                'message' => "Pickup Adresses",
                'data' =>  $store->pickupAddresses()->get(),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }

    function getWalletBalance()
    {

        try {
            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            $response = [
                'status' => true,
                'message' => "Wallet Balance",
                'data' => calculateStoreWallet($store)['freeBalance'],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }


    public function dues(Request $request)
    {
        try {
            $this->authorize('isEmployeeHasPermission', 'dues_stores');
            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            $response = [
                'status' => true,
                'message' => "Dues",
                'data' =>    [

                    getStoreDues($store),
                 ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e,
            ], 500);
        }
    }
}
