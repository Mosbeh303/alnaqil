<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Shipment;
use App\Models\Voucher;
use App\Models\Groupe;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\City;
use App\Models\Branch;
use App\Models\Custody;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request AS RequestFacade;
use Meneses\LaravelMpdf\Facades\LaravelMpdf AS PDF ;
use Illuminate\Validation\Rule;

class OperatorController extends Controller
{

    public function index(Request $request)
    {
        if ($request->type == 'rec' )
            $this->authorize('isEmployeeHasPermission', 'show_rec_operators');

        if ($request->type == 'del' )
            $this->authorize('isEmployeeHasPermission', 'show_del_operators');

        if ($request->type == 'vip' )
            $this->authorize('isEmployeeHasPermission', 'show_vip_operators');

        $groupe = null ;
        if ($request->groupe)
            $groupe = $request->groupe;


        return Inertia::render('Operators/Index', [
            'filters'   => RequestFacade::all('search', 'type'),
            'Operators' => Operator::whereHas('user', function ($query) use ($request){
                return $query->where('type', 'operator')->where('groupe_id', $request->groupe);
                })
                ->orderBy('created_at', 'desc')
                ->filter(RequestFacade::only('search', 'type'))
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($Operator) => [
                    'id' => $Operator->id,
                    'name' => $Operator->user->name,
                    'lastname' => $Operator->user->lastname,
                    'username' => $Operator->user->username,
                    'email' => $Operator->user->email,
                    'phone' => $Operator->user->phone,
                    'unpaid_dues' => $Operator->unpaid_dues,
                    'custodies' => $Operator->user->custodies()->sum('amount') ?? 0,
                    'shipments' => $Operator->shipments()->count(),
                    'type' => $Operator->type,
                    'city' => $Operator->city->name ?? null ,
                    'branch' =>  $Operator->branch->name  ?? null ,
                    'neighborhood' => $Operator->neighborhood,
                    'allowed' => $Operator->allowed,
                    'identification' => $Operator->identification,

                ]),
            'type' => RequestFacade::only('type'),
            'groupe' => Groupe::find($groupe),
        ]);


    }

    public function create(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_operators');
        $groupe = null ;
        if ($request->groupe)
            $groupe = $request->groupe;
        return Inertia::render('Operators/Create', [
            'type' => $request->type,
            'groupe' => $groupe,
            'cities' => City::where('type' , 'register')->get() ?? [trans('alriyadh')],
        ]);
    }


    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_operators');
        $request->validate([
            'name' => 'required|string|max:255',
            'type'  => 'required',
            'phone' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('type', 'operator');
            })],
            'email' => 'required|string|email|max:255|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
            'identification' => ['numeric', 'unique:operators'],
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
            'type' => 'operator',
            'groupe_id' => $request->groupe,
        ]);

        $operator = $user->Operator()->create([
            'type' => $request->type,
            'city_id' => $request->city,
            'branch_id' => $request->branch,
            'neighborhood' => $request->neighborhood,
            'identification' => $request->identification
        ]);

        event(new Registered($user));

        return redirect::back()->with('success', trans('delegate_added_successfully'));

    }

    public function updateStatus(Operator $operator)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_operators');
        if ($operator->allowed)
            $allowed = 0 ;
        else
            $allowed = 1 ;

        $operator->update([
            'allowed' => $allowed
        ]);

        return redirect::back()->with('success', trans('delegate_status_updated_successfully'));

    }

    public function shipmentsPdf($id, Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'show_operators');

        $operator = Operator::findOrFail($id);

        $shipments = Shipment::with('financial')
        ->where('operator_id', $id)
        ->whereIn('status', [10,20,35,65])
        ->when($request->shipments ?? null, function($query) use ($request){
            $query->whereIn('number', $request->shipments);
        })
        ->orderBy('status', 'asc')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn ($shipment) => [
            'id' => $shipment->id,
            'number' => $shipment->number,
            'cod' => $shipment->financial->cod + 0,
            'receiverPhone' => $shipment->receiver_phone,
            'receiverName' => $shipment->receiver_name,
            'storeName' => $shipment->store->name,
            'city' => $shipment->city,
            'neighborhood' => $shipment->neighborhood,
            'street' => $shipment->street,
            'status' => $shipment->status,
            'number_of_pieces' => $shipment->number_of_pieces,
            'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : '',
        ]);

        $operatorName = $operator->user->name;
        $userAccount = auth()->user()->type ;

        $pdf = PDF::loadView('pdf/operator_shipments', compact('shipments','userAccount', 'operatorName'),[], ['format'  => 'A4', 'mode' => 'landscape']) ;
        return $pdf->download('shipments.pdf');
    }


    public function show(Operator $operator, Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'show_operators');

        return Inertia::render('Operators/Show', [
            'operator' => [
                'id' => $operator->id,
                'user_id' => $operator->user->id,
                'type' => $operator->type,
                'username' => $operator->user->username,
                'name' => $operator->user->name,
                'dues' => $operator->unpaid_dues
            ],
            'stats' => [
                'primary' => [
                    'all' => $operator->shipments->count(),
                    'completed' => $operator->user->shipmentTracks()->where('action', 'completed')->count(),
                    'returned' => $operator->user->shipmentTracks()->where('action', 'returned')->count(),
                    'custodies' => $operator->user->custodies()->sum('amount'),
                ],
                'daily' => [
                    'all' => $operator->dailyShipments()->whereHas('shipment', function ($query) {
                        return $query->where('status', 100);
                    })->count(),
                    'paid' => $operator->dailyShipments()->whereHas('shipment', function ($query) {
                        return $query->where('status', 100)->whereHas('financial', function ($query) {
                            return $query->where('cod', null)->orWhere('cod', 0);
                        });
                    })->count(),
                    'cash' => $operator->dailyShipments()->whereHas('shipment', function ($query) {
                        return $query->where('status', 100)->whereHas('financial', function ($query) {
                            return $query->where('cod', '>', 0)->where('payment_method', 'cash');
                        });
                    })->count(),
                    'epayment' => $operator->dailyShipments()->whereHas('shipment', function ($query) {
                        return $query->where('status', 100)->whereHas('financial', function ($query) {
                            return $query->where('cod', '>', 0)->where('payment_method', 'epayment');
                        });
                    })->count(),
                ],
            ],
            'filters' => RequestFacade::all('search', 'status'),
            'shipments' => Shipment::with('financial')
                ->where('operator_id', $operator->id)
                ->whereIn('status', [100, -100])
                ->when($request->search ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('number', 'like', '%'.$search.'%')
                            ->orWhere('city', 'like', '%'.$search.'%')
                            ->orWhere('receiver_phone', 'like', '%'.$search.'%')
                            ->orWhere('warehouse_location', 'like', '%'.$search.'%')
                            ->orWhereHas('store', function ($query) use ($search) {
                                $query->where('name', 'like', '%'.$search.'%');
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'shipping_date' => $shipment->shipping_date ,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'city' => $shipment->city,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : 'لا يوجد ملاحظات',
                ]),

            'receivedShipments' => Shipment::with('financial')
                ->where('operator_id', $operator->id)
                ->whereIn('status', [10,20,35,65])
                ->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'shipping_date' => $shipment->shipping_date ,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'city' => $shipment->city,
                    'neighborhood' => $shipment->neighborhood,
                    'receiverName' => $shipment->receiver_name,
                    'receiverPhone' => $shipment->receiver_phone,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : '',
                ]),

            'vouchers' => Voucher::where('user_id', $operator->user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($voucher) => [
                    'id' => $voucher->id,
                    'number' => 'M' . $voucher->number,
                    'employee' => $voucher->employee->user->name ?? null,
                    'amount' => $voucher->amount,
                    'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                    'notice' => $voucher->notice,
                ]),

            'custodies' => Custody::where('user_id', $operator->user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($voucher) => [
                    'id' => $voucher->id,
                    'number' => $voucher->number,
                    'amount' => $voucher->amount,
                    'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                    'notice' => $voucher->notice,
                ]),
        ]);
    }


    public function edit(Operator $operator)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_operators');

        return Inertia::render('Operators/Edit', [
            'operator' => [
                'id' => $operator->id,
                'userId' => $operator->user_id,
                'type' => $operator->type,
                'username' => $operator->user->username,
                'name' => $operator->user->name,
                'phone' => $operator->user->phone,
                'email' => $operator->user->email,
                'city' => $operator->city->id ?? null,
                'neighborhood' => $operator->neighborhood,
                'identification' => $operator->identification,
                'branch_id' => $operator->branch_id,
            ],
            'cities' => City::where('type' , 'register')->get() ?? [trans('alriyadh')],
            'branchesProps' => $operator->city->branches ?? null

        ]);
    }


    public function update(Request $request, Operator $operator)
    {

        $this->authorize('isEmployeeHasPermission', 'edit_operators');

        $request->validate([
            'name' => 'required|string|max:255',
            'type'  => 'required',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|string|email|max:255|unique:users,email,'.$operator->user->id,
            'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/u', 'unique:users,username,'.$operator->user->id ],
            'identification' => ['numeric', 'unique:operators,identification,'.$operator->id ],
        ],
        [

        ]);

        $operator->update([
            'type' => $request->type,
            'city_id' => $request->city,
            'branch_id' => $request->branch,
            'neighborhood' => $request->neighborhood,
            'identification' => $request->identification,
        ]);


        $operator->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->username,

        ]);

        return redirect::back()->with('success',trans('delegate_data_updated_successfully'));
    }


    public function reset(Operator $operator)
    {
        $this->authorize('isEmployeeHasPermission', 'show_operators');

        $operator->dailyShipments()->delete();
        return redirect::back()->with('success', trans('delegate_daily_data_successfully_reset'));
    }



    public function shipments(Operator $operator, Request $request){
        $daily = $operator->dailyShipments()->whereHas('shipment', function ($query) {
            return $query->where('status', 100);
        })->count();

        return Inertia::render('Shipments', [
            'operator' => [
                'id' => $operator->id,
                'type' => $operator->type,
                'username' => $operator->user->username,
                'name' => $operator->user->name,
                'dues' => $operator->unpaid_dues
            ],

            'filters' => RequestFacade::all('search', 'method'),
            'shipments' => Shipment::with('financial')
                ->where('operator_id', $operator->id)
                ->where('status', 100)
                ->when($daily == 0, function ($query) {
                    $query->where('number', 0);
                })
                ->when($request->search ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('number', 'like', '%'.$search.'%')
                            ->orWhere('city', 'like', '%'.$search.'%')
                            ->orWhere('receiver_phone', 'like', '%'.$search.'%')
                            ->orWhere('warehouse_location', 'like', '%'.$search.'%')
                            ->orWhereHas('store', function ($query) use ($search) {
                                $query->where('name', 'like', '%'.$search.'%');
                            });
                    });
                })->when($request->method == 'prepaid', function ($query) {
                            $query->whereHas('financial', function ($query)  {
                                $query->where('cod', null)->orWhere('cod', 0);
                            });
                } , function ($query) use ($request) {
                        $query->whereHas('financial', function ($query) use ($request) {
                                $query->where('payment_method', $request->method);
                    });

                })->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                    'shipping_date' => $shipment->shipping_date ,
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


}