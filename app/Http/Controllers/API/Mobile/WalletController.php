<?php

namespace App\Http\Controllers\API\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wallet; // Banks
use App\Models\Store;
use DB;
use App\Models\Voucher;
use Illuminate\Support\Facades\Validator;
use Auth;


class WalletController extends Controller
{
    public function show()
    {

        try {

            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            $response = [
                'status' => true,
                'message' => "Wallet",
                'data' =>  [
                    'vouchers' => Voucher::where('user_id', $store->user->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(50)
                        ->withQueryString()
                        ->through(fn ($voucher) => [
                            'id' => $voucher->id,
                            'number' => $voucher->number,
                            'amount' => $voucher->amount,
                            'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                            'notice' => $voucher->notice,
                            'to_bank' => $voucher->to_bank,
                            'bank' => $voucher->wallet,
                            'employee' => $voucher->employee->user ?? null,
                            'belongsToStoreWallet' => $voucher->belongsToStoreWallet
                        ]),
                    ],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Log the exception message
   \Log::error($e);
           // something went wrong
           return response()->json([
               'code' => 500,
               'success' => false,
               'message' => "Something went wrong",
               'errors' => $e->getMessage(),
           ], 500);
       }
    }


    public function recharge(Request $request)
    {
        try {
            $user = Auth::user();
            Validator::make($request->all(),
            [
                [
                    'amount' => 'required|numeric|gt:0',
                ],
                [
                    'amount.required' => trans('amount_required'),
                    'type.required' => trans('Bond_type_required'),
                    'wallet.required' => trans('bank_name_is_required')
                ]
            ]);


            $store = Store::where('id', auth('sanctum')->user()->store->id)->first();

            $amount =  - ($request->amount);

            $max = DB::table('vouchers')->where('type', 'RECEIPT')->max('number') + 1;



            $voucher = $store->user->vouchers()->create([
                'to_bank'   => $request->bank,
                'number' => $max,
                'amount' => $amount,
                //'notice' => $request->notice,
                'type'   => "RECEIPT",
                'wallet_id' => 2,
                //'folder_wallet_id' => $request->folderWallet,
                'belongsToStoreWallet' => 1,
               // 'employee_id' => auth()->user()->employee->id ?? null
            ]);


            $response = [
                'status' => true,
                'message' => trans('voutcher_added_successfully'),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
             // Log the exception message
    \Log::error($e);
            // something went wrong
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => "Something went wrong",
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
