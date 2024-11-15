<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use DB ;
use Meneses\LaravelMpdf\Facades\LaravelMpdf AS PDF ;

class VoucherController extends Controller
{

    public function index(){
        $this->authorize('isEmployeeHasPermission', 'vouchers_stores');

        return Inertia::render('Stores/Vouchers');
    }

    public function getVouchers(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'vouchers_stores');


        if (!$request->number)
            $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);



        $from = date('Y-m-d', strtotime($request->from )) ;
        $to = date('Y-m-d',strtotime($request->to . "+1 day"));



        $vouchers = Voucher::when($request->number ?? null, function ($query)  use ($request) {
                    $query->where('number', $request->number);
                })
                ->when($request->from  ?? null, function ($query) use ($from, $to) {
                    $query->whereBetween('created_at' , [$from ,$to ]);
                })
                ->when($request->type != null, function ($query)  use ($request){
                        $query->where('type', $request->type);
                })
                ->when($request->type == null, function ($query) {
                    $query->whereIn('type', ['RECEIPT','PAYMENT']);
                })
                ->get()
                ->map(fn ($voucher) => [
                    'id' => $voucher->id,
                    'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                    'amount' => $voucher->amount ,
                    'store' => $voucher->user->store->name ?? null ,
                    'number' => $voucher->number,
                    'type' => $voucher->type,
                    'bank' => $voucher->wallet->name ?? null ,
                    'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                    'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                ]);



        return Inertia::render('Stores/Vouchers',[
                'items' => $vouchers,
        ]);
    }

    public function vouchersPdf(Request $request){

        $this->authorize('isEmployeeHasPermission', 'vouchers_stores');

        if (!$request->number)
            $request->validate([
                'to' => 'required',
                'from' => 'required',
            ]);



        $from = date('Y-m-d', strtotime($request->from )) ;
        $to = date('Y-m-d',strtotime($request->to . "+1 day"));



        $items = Voucher::when($request->number ?? null, function ($query)  use ($request) {
                    $query->where('number', $request->number);
                })
                ->when($request->from  ?? null, function ($query) use ($from, $to) {
                    $query->whereBetween('created_at' , [$from ,$to ]);
                })
                ->when($request->type != null, function ($query)  use ($request){
                        $query->where('type', $request->type);
                })
                ->when($request->type == null, function ($query) {
                    $query->where('type', 'RECEIPT')
                    ->orWhere('type', 'PAYMENT');
                })
                ->get()
                ->map(fn ($voucher) => [
                    'id' => $voucher->number,
                    'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                    'amount' => $voucher->amount ,
                    'store' => $voucher->user->store->name ?? null ,
                    'number' => $voucher->number,
                    'type' => $voucher->type,
                    'bank' => $voucher->wallet->name ?? null ,
                    'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                    'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                ]);


        $pdf = PDF::loadView('pdf/vouchers-summary', compact('items', 'to', 'from'),[], ['format'  => 'A4']) ;
        return $pdf->download('vouchers.pdf');
    }

    public function store(Request $request, User $user = null)
    {
        //! $this->authorize('isEmployeeHasPermission', 'show_operators');
        if($user === null || $user->type == 'client' || $user->type == 'marketer' ){
            $request->validate([
                'type'  => 'required',
                'amount' => 'required|numeric|gt:0',
                'wallet' => 'required'
            ],
            [
                'amount.required' => trans('amount_required'),
                'type.required' => trans('Bond_type_required'),
                'wallet.required' => trans('bank_name_is_required')
            ]);



            $amount =  $request->type == "-" ? -($request->amount) : $request->amount;

            if($request->type == "-"){
                $max = DB::table('vouchers')->where('type', 'RECEIPT')->max('number') + 1;
                $type = 'RECEIPT' ;
            }else {
                $max = DB::table('vouchers')->where('type', 'PAYMENT')->max('number') + 1;
                $type = 'PAYMENT' ;
            }



            if ($user){

                $voucher = $user->vouchers()->create([
                    'to_bank'   => $request->bank,
                    'number' => $max,
                    'amount' => $amount,
                    'notice' => $request->notice,
                    'type'   => $type,
                    'wallet_id' => $request->wallet,
                    'folder_wallet_id' => $request->folderWallet,
                    'belongsToStoreWallet' => $request->belongsToStoreWallet ? 1 : 0,
                    'employee_id' => auth()->user()->employee->id ?? null
                ]);

                if (!$request->belongsToStoreWallet && $user->type == 'client')
                    $user->store()->increment('dues', $amount);

            } else {
                $voucher = Voucher::create([
                    'to_bank'   => $request->bank,
                    'number' => $max,
                    'amount' => $amount,
                    'notice' => $request->notice,
                    'type'   => $type,
                    'wallet_id' => $request->wallet,
                    'folder_wallet_id' => $request->folderWallet,
                    'employee_id' => auth()->user()->employee->id ?? null,
                    'name' => $request->name
                ]);
            }

            $voucher->wallet()->decrement('balance', $amount);

            $voucher->folderWallet()->decrement('balance', $amount);

            return redirect::back()->with('success', trans('voutcher_added_successfully'));
        }

        if($user->type == 'operator'){
            $request->validate([
                'amount' => 'required|numeric|gt:0',
            ],
            [
                'amount.required' => 'المبلغ مطلوب'
            ]);

            $employee = null ;
            if (auth()->user()->type == 'employee'){
                $employee = auth()->user()->employee->id;
                auth()->user()->employee->increment('dues', $request->amount);
            }

            $max = DB::table('vouchers')->where('type', 'operator')->max('number') + 1;

            $user->vouchers()->create([
                'employee_id' => $employee,
                'number' => $max,
                'amount' => $request->amount,
                'notice' => $request->notice,
                'type'   => 'operator'
            ]);

            $user->operator->decrement('unpaid_dues', $request->amount);

            return redirect::back()->with('success',trans('redemption_added_successfully'));
        }



    }


    public function destroy(Voucher $voucher){
        if($voucher->user->operator ?? null)
            $voucher->user->operator()->increment('unpaid_dues', $voucher->amount);

        if ($voucher->user->store ?? null){
            $voucher->user->store()->decrement('dues', $voucher->amount);
            $voucher->wallet()->increment('balance', $voucher->amount);
        }

        if ($voucher->folderWallet()){
            $voucher->folderWallet()->increment('balance', $voucher->amount);
        }


        $voucher->delete();
        return back()->with('success', trans('redemption_deleted_successfully'));
    }

    public function update(Voucher $voucher, Request $request){

        $amount =  $request->type == "-" ? -($request->amount) : $request->amount;
        $diff = $voucher->amount - $amount;

        $number = $voucher->number;
        $type = $voucher->type;

        if($request->type == "-" && $type == 'PAYMENT'){
            $number = DB::table('vouchers')->where('type', 'RECEIPT')->max('number') + 1;
            $type = 'RECEIPT' ;
        }elseif ($request->type == "+" && $type == 'RECEIPT') {
            $number = DB::table('vouchers')->where('type', 'PAYMENT')->max('number') + 1;
            $type = 'PAYMENT' ;
        }

        $old_wallet =  $voucher->wallet;

        $voucher->update([
            'amount' => $amount,
            'number' => $number,
            'type' => $type,
            'to_bank'   => $request->bank,
            'wallet_id'   => $request->wallet,
            'notice' => $request->notice,
            'name' => $request->name,
            'belongsToStoreWallet' => $request->belongsToStoreWallet ?? 0 ,
        ]);

        if ($voucher->user && $voucher->user->store){
            $voucher->user->store()->decrement('dues', $diff);
        }

        if ($voucher->user && $voucher->user->operator){

            $voucher->user->operator()->increment('unpaid_dues', $diff);
            if ($voucher->employee_id)
                $voucher->employee->decrement('dues', $diff);
        } else {
            if($voucher->wallet_id === $old_wallet->id){
                $voucher->wallet()->increment('balance', $diff);
            } else {
                $voucher->wallet()->decrement('balance', $amount);
                $old_wallet->increment('balance', $amount);
            }
        }

        if($voucher->folderWallet)
            $voucher->folderWallet->increment('balance', $diff);



        return redirect::back()->with('success', trans('redemption_updated_successfully'));
    }

    public function show(Voucher $voucher, Request $request){

        //! $this->authorize('isEmployeeHasPermission', 'show_operators');

        return Inertia::render('Vouchers/Show', [
            'voucher' => [
                'id' => $voucher->id,
                'number' => $voucher->number,
                'to_bank' => $voucher->to_bank ?? trans('cash'),
                'bank' => $voucher->wallet->name ?? trans('cash'),
                'amount' => $voucher->amount,
                'storeName' => $voucher->user->store->name ?? null,
                'name' => $voucher->user->name ?? $voucher->name ?? null,
                'notice' => $voucher->notice,
                'date' => $voucher->created_at->format('Y/m/d'),
                'type' => $voucher->type
            ],

        ]);

    }
}
