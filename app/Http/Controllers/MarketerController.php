<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Marketer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS RequestFacade;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Store;
use App\Models\Voucher;
use App\Models\Wallet;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommissionExport;

class MarketerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Marketers/Index', [
            'filters'   => RequestFacade::all('search', 'type'),
            'marketers' => Marketer::whereHas('user')
                ->orderBy('created_at', 'desc')
                ->filter(RequestFacade::only('search'))
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($marketer) => [
                    'id' => $marketer->id,
                    'name' => $marketer->user->name,
                    'lastname' => $marketer->user->lastname,
                    'username' => $marketer->user->username,
                    'email' => $marketer->user->email,
                    'phone' => $marketer->user->phone,
                    'commission' => $marketer->commission_value,
                    'commission_type' => $marketer->commission_type,
                    'dues' => 0,
                    'shipments' => $marketer->commissions()->count(),
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('isEmployeeHasPermission', 'create_marketers');

        return Inertia::render('Marketers/Create', [

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_marketers');
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('type', 'employee');
            })],
            'email' => 'required|string|email|max:255|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
            'password' => ['required', Rules\Password::min(6)],
        ],
        [

        ]);


        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'type' => 'marketer',
        ]);

        $user->marketer()->create([
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission,
        ]);

        event(new Registered($user));

        return back()->with('success', trans('added_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function show(Marketer $marketer)
    {
        $this->authorize('isEmployeeHasPermission', 'show_marketers');

        return Inertia::render('Marketers/Show', [
            'filters' => RequestFacade::all('search', 'status'),
            'marketer' => [
                'id' => $marketer->id,
                'name' => $marketer->user->name,
                'username' => $marketer->user->username,
                'password' => $marketer->user->password,
                'phone' => $marketer->user->phone,
                'email' => $marketer->user->email,
                'commission_type' => $marketer->commission_type,
                'commission' => $marketer->commission_value,
                'user_id' => $marketer->user_id,
            ],

            'commissions' => Commission::orderBy('id', 'desc')
                ->where('marketer_id', $marketer->id)
               // ->filter(RequestFacade::only('search', 'status'))
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($commission) => [
                    'id' => $commission->id,
                    'amount' => $commission->amount,
                    'shipment' => [
                        'number' => $commission->shipment->number,
                        'fees'   => number_format((float)(calculateFees($commission->shipment->financial) / 1.15), 2, '.', '') ,
                    ]
                ]),

            'stores' => Store::orderBy('id', 'desc')
                ->where('marketer_id', $marketer->id)
                ->filter(RequestFacade::only('search', 'status'))
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'type' => $store->type,
                    'ownerName' => $store->user->name,
                    'status'   => $store->status,
                    'city'     => $store->city,
                    'neighborhood' => $store->neighborhood,
                    'phone' => $store->user->phone,
                    'shipments' => $store->shipments->count(),
                ]),

            'vouchers' => Voucher::where('user_id', $marketer->user->id)
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
            'wallets' => Wallet::all(),

            'stats' => [
                'dues' =>  getMarketerDues($marketer),
                'shipments_number' => $marketer->commissions()->count(),
                'stores_number' => $marketer->stores()->count(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function edit(Marketer $marketer)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_marketers');

        return Inertia::render('Marketers/Edit', [
            'marketer' => [
                'id' => $marketer->id,
                'name' => $marketer->user->name,
                'username' => $marketer->user->username,
                'password' => $marketer->user->password,
                'phone' => $marketer->user->phone,
                'email' => $marketer->user->email,
                'commission_type' => $marketer->commission_type,
                'commission' => $marketer->commission_value,
                'user_id' => $marketer->user_id,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marketer $marketer)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_marketers');
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('type', 'employee');
            })],
            'email' => 'required|string|email|max:255|unique:users,email,'.$marketer->user->id,
            'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/u', 'unique:users,username,'.$marketer->user->id],
        ],
        [

        ]);


        $marketer->user->update([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'type' => 'marketer',
        ]);

        $marketer->update([
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission,
        ]);

        return back()->with('success',trans('updated_successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketer $marketer)
    {
        //
    }

    public function clients()
    {

        return Inertia::render('Stores/Index', [
            'filters' => RequestFacade::all('search', 'status'),
            'stores' => Store::orderBy('id', 'desc')
                ->where('marketer_id', auth()->user()->marketer->id)
                ->filter(RequestFacade::only('search', 'status'))
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'type' => $store->type,
                    'ownerName' => $store->user->name,
                    'status'   => $store->status,
                    'city'     => $store->city,
                    'neighborhood' => $store->neighborhood,
                    'phone' => $store->user->phone,
                    'shipments' => $store->shipments->count(),
                ]),
        ]);
    }

    public function commissions(Request $request, Marketer $marketer = null )
    {

        $this->authorize('isEmployeeHasPermission', 'show_marketers');

        if (auth()->user()->type == 'marketer')
          $marketer = auth()->user()->marketer;

        elseif (!$marketer)
            abort(404);




        $commissions = Commission::orderBy('id', 'desc')
        ->where('marketer_id', $marketer->id)
        ->when(($request->from ?? null && $request->to ?? null), function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from  , date('Y-m-d',strtotime($request->to . "+1 day"))]);
        })
        ->paginate(50)
        ->withQueryString()
        ->through(fn ($commission) => [
            'id' => $commission->id,
            'amount' => $commission->amount,
            'shipment' => [
                'store' => $commission->shipment->store->name,
                'number' => $commission->shipment->number,
                'status' => $commission->shipment->status,
                'shipping_date' => $commission->shipment->updated_at->format('Y-m-d H:i:s'),
                'fees'   => number_format((float)(calculateFees($commission->shipment->financial) / 1.15), 2, '.', ''),
            ]
        ]);

        $totalAmount = Commission::orderBy('id', 'desc')
        ->where('marketer_id', $marketer->id)
        ->when(($request->from ?? null && $request->to ?? null), function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from  , date('Y-m-d',strtotime($request->to . "+1 day"))]);
        })
        ->sum('amount');

        return Inertia::render('Marketers/Commissions', [
           // 'filters' => RequestFacade::all('search', 'status'),
            'commissions' => $commissions,
            'from' => $request->from,
            'to' => $request->to,
            'totalAmount' => $totalAmount,
            'marketer' => [
                'id' => $marketer->id,
                'name' => $marketer->user->name
            ]
        ]);
    }

    public function exportComissions(Request $request, Marketer $marketer = null )
    {

        $this->authorize('isEmployeeHasPermission', 'show_marketers');

        if (auth()->user()->type == 'marketer')
          $marketerId = auth()->user()->marketer->id;

        elseif ($marketer)
          $marketerId = $marketer->id;

        else
          abort(404);


        $commissions = Commission::orderBy('id', 'desc')
        ->where('marketer_id',  $marketerId)
        ->when(($request->from ?? null && $request->to ?? null), function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from  , date('Y-m-d',strtotime($request->to . "+1 day"))]);
        })
        ->paginate(50)
        ->withQueryString()
        ->through(fn ($commission) => [
            'id' => $commission->id,
            'amount' => $commission->amount,
            'shipment' => [
                'store' => $commission->shipment->store->name,
                'number' => $commission->shipment->number,
                'status' => $commission->shipment->status,
                'shipping_date' => $commission->shipment->updated_at->format('Y-m-d H:i:s'),
                'fees'   => number_format((float)(calculateFees($commission->shipment->financial) / 1.15), 2, '.', ''),
            ]
        ]);

        return Excel::download(new CommissionExport($commissions), 'commissions.xlsx');
    }


}
