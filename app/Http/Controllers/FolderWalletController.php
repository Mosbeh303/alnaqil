<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\FolderWallet;
use App\Models\Wallet; // Banks
use Meneses\LaravelMpdf\Facades\LaravelMpdf AS PDF ;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WalletExport;

class FolderWalletController extends Controller
{
    public function store(Request $request, Folder $folder){

        $this->authorize('isEmployeeHasPermission', 'wallets_accounts');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [

        ]);

        $folder->wallets()->create([
            'name' => $request->name,
            'balance' => 0,
        ]);


        return back()->with('success', trans('bank_added_successfully'));
    }

    public function edit(FolderWallet $wallet){

        $this->authorize('isEmployeeHasPermission', 'wallets_accounts');

        return inertia('Accounts/Wallets/Edit', ['wallet' => $wallet]);
    }

    public function update(FolderWallet $wallet, Request $request){

        $this->authorize('isEmployeeHasPermission', 'wallets_accounts');

        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
        ],
        [

        ]);

        $data = $wallet->update([
            'name' => $request->name,
            'balance' => $request->balance,
        ]);

        return back()->with('success', trans('wallet_updated_successfully'));
    }

    public function show(FolderWallet $wallet, Request $request){

        $this->authorize('isEmployeeHasPermission', 'wallets_accounts');

        if ($request->from && $request->to){
            $request->validate([
                'from' => 'required',
                'to' => 'required',
            ],
            [
                'from.required' => trans('required_field'),
                'to.required' => trans('required_field') ,
            ]);

            $from = date('Y-m-d', strtotime($request->from )) ;
            $to = date('Y-m-d',strtotime($request->to . "+1 day"));

            $sum = $wallet->vouchers()->where('created_at' , '<' ,$from )->sum('amount');

            return inertia('Accounts/Wallets/Show', [
                'wallet' => $wallet,
                'vouchers' => $wallet->vouchers()->whereBetween('created_at' , [$from ,$to ])->get()->map(fn ($voucher) => [
                    'id' => $voucher->id,
                    'number' => $voucher->number,
                    'amount' => $voucher->amount,
                    'type' => $voucher->type,
                    'to_bank' => $voucher->to_bank,
                    'created_at' => $voucher->created_at,
                    'notice' => $voucher->notice,
                    'name' => $voucher->user->store->name ?? $voucher->name,
                    'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                    'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                ]),
                'to' => $request->to,
                'from' => $request->from,
                'creditorSum' => $wallet->vouchers()->where('amount', '<', 0)->whereBetween('created_at' , [$from ,$to ])->sum('amount'),
                'debtorSum' => $wallet->vouchers()->where('amount', '>', 0)->whereBetween('created_at' , [$from ,$to ])->sum('amount'),
                'initaielBalance' => - floatval($sum),
                'wallets' => Wallet::all()
            ]);
        }

        return inertia('Accounts/Wallets/Show', [
            'wallet' => $wallet,
            'vouchers' => $wallet->vouchers()->get()->map(fn ($voucher) => [
                'id' => $voucher->id,
                'number' => $voucher->number,
                'amount' => $voucher->amount,
                'type' => $voucher->type,
                'to_bank' => $voucher->to_bank,
                'created_at' => $voucher->created_at,
                'notice' => $voucher->notice,
                'name' => $voucher->user->store->name ?? $voucher->name,
                'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
                'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
            ]),
            'creditorSum' => $wallet->vouchers()->where('amount', '<', 0)->sum('amount'),
            'debtorSum' => $wallet->vouchers()->where('amount', '>', 0)->sum('amount'),
            'to' => null,
            'from' => null,
            'initaielBalance' => 0,
            'wallets' => Wallet::all()
        ]);
    }

    public function pdf(FolderWallet $wallet, Request $request)
    {
        // $this->authorize('isEmployeeHasPermission', 'summary_shipments');
        if ($request->from || $request->to){
            $request->validate([
                'from' => 'required',
                'to' => 'required',
            ],
            [
                'from.required' => trans('required_field'),
                'to.required' => trans('required_field'),
            ]);
        }

        $from = date('Y-m-d', strtotime($request->from )) ;
        $to = date('Y-m-d',strtotime($request->to . "+1 day"));
        $sum = $wallet->vouchers()->where('created_at' , '<' ,$from )->sum('amount');


        $vouchers = $wallet->vouchers()
        ->when($request->from ?? null , function ($query) use ($request){
            $query->whereBetween('created_at' , [$request->from  , date('Y-m-d',strtotime($request->to . "+1 day"))]);
        })->get()->map(fn ($voucher) => [
            'id' => $voucher->id,
            'number' => $voucher->number,
            'amount' => $voucher->amount,
            'type' => $voucher->type,
            'to_bank' => $voucher->to_bank,
            'created_at' => $voucher->created_at,
            'notice' => $voucher->notice,
            'store' => $voucher->user->store->name ?? $voucher->name,
            'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
            'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
            'sum' => 0
        ]);

        $vouchers = $vouchers->toArray();

        foreach($vouchers as &$voucher){
            $sum = $sum - $voucher['amount'];

            $voucher['sum'] = $sum;

        }

        // $vouchers->sortByDesc('id')->values()->all();
        //$vouchers = array_reverse($vouchers);

        $to = date('Y-m-d',strtotime($request->to ));

        $pdf = PDF::loadView('pdf/wallet', compact('vouchers','wallet', 'from', 'to'),[], ['format'  => 'A4', 'mode' => 'landscape']) ;
        return $pdf->download($wallet->name . '.pdf');
    }

    public function export(FolderWallet $wallet, Request $request)
    {
        if ($request->from || $request->to){
            $request->validate([
                'from' => 'required',
                'to' => 'required',
            ],
            [
                'from.required' => trans('required_field'),
                'to.required' => trans('required_field'),
            ]);
        }

        $from = date('Y-m-d', strtotime($request->from )) ;
        $to = date('Y-m-d',strtotime($request->to . "+1 day"));
        $sum = $wallet->vouchers()->where('created_at' , '<' ,$from )->sum('amount');

        $vouchers = $wallet->vouchers()->when($request->from ?? null , function ($query) use ($request){
            $query->whereBetween('created_at' , [$request->from  , date('Y-m-d',strtotime($request->to . "+1 day"))]);
        })->get()->map(fn ($voucher) => [
            'id' => $voucher->id,
            'number' => $voucher->number,
            'amount' => $voucher->amount,
            'type' => $voucher->type,
            'to_bank' => $voucher->to_bank,
            'created_at' => $voucher->created_at,
            'notice' => $voucher->notice,
            'store' => $voucher->user->store->name ?? $voucher->name,
            'employee' => $voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
            'responsible' => !$voucher->user ? ($voucher->employee->user->name ?? "Admin") : null,
            'sum' => 0
        ]);

        $vouchers = $vouchers->toArray();

        foreach($vouchers as &$voucher){
            $sum = $sum - $voucher['amount'];

            $voucher['sum'] = $sum;

        }

        // $vouchers->sortByDesc('id')->values()->all();
        $vouchers = array_reverse($vouchers);

         return Excel::download(new WalletExport($vouchers), 'wallets.xlsx');
    }
}
