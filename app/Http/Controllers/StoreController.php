<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Financial;
use App\Models\Shipment;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\City;
use App\Models\Operator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Auth;
use DB;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DuesExport;
use App\Exports\storeSummaryExport;
use App\Exports\StoresWalletsExport;
use App\Exports\StoresExport;
use App\Models\PickupAddress;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class StoreController extends Controller
{
    public function index()
    {
        $this->authorize('isEmployeeHasPermission', 'index_stores');

        return Inertia::render('Stores/Index', [
            'filters' => RequestFacade::all('search', 'status'),
            'stores' => Store::orderBy('id', 'desc')
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

    public function create(){
        $this->authorize('isEmployeeHasPermission', 'create_stores');

        return Inertia::render('Stores/Create', [
            'cities' => City::where('type', 'register')->get() ?? [trans('alriyadh')],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'ownerName' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'id_number' => 'nullable|numeric|digits:10',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
            'password' => ['required', Rules\Password::min(6)],

        ]);

        $user = User::create([
            'name' => $request->ownerName ?? $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'id_number' => $request->id_number
        ]);

        $base_fee = floatval(Setting::where('option', 'base_fee')->value('value'));
        $exchange_fee = floatval(Setting::where('option', 'exchange_fee')->value('value'));
        $refrigerated_transport_fee = floatval(Setting::where('option', 'refrigerated_transport_fee')->value('value'));
        $return_fee = floatval(Setting::where('option', 'return_fee')->value('value'));

        $store = $user->store()->create([
            'name' => $request->name,
            'website' => $request->website,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'base_fee' => $base_fee,
            'exchange_fee' => $exchange_fee,
            'refrigerated_transport_fee' => $refrigerated_transport_fee,
            'extra_fees' => $return_fee,
            'type' => $request->type ?? 'shop',
            'marketer_id' => auth()->user()->type == 'marketer' ? auth()->user()->marketer->id : null
        ]);

        // $store->bankAccount()->create([
        //     'bank_name' => $request->bankName,
        //     'owner_name' => $request->ownerName,
        //     'rib' => $request->rib,
        // ]);

        event(new Registered($user));

        if (auth()->user()->type == 'marketer')
            return back()->with('success', trans('added_successfully'));
        else
         return Redirect::route('stores.edit', $store);




    }

    public function edit(Store $store)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_stores');

        return Inertia::render('Stores/Edit', [
            'store' => [
                'id' => $store->id,
                'userId' => $store->user_id,
                'name' => $store->name,
                'type' => $store->type,
                'status' => $store->status,
                'username' => $store->user->username,
                'ownerName' => $store->user->name,
                'id_number' => $store->user->id_number,
                'phone' => $store->user->phone,
                'email' => $store->user->email,
                'city' => $store->city,
                'neighborhood' => $store->neighborhood,
                'govNumber' => $store->gov_number,
                'taxNumber' => $store->tax_number,
                'maaroof' => $store->maaroof,
                'freelanceDocument' => $store->freelance_document,
                'baseFee' => $store->base_fee,
                'baseFee2' => $store->base_fee2,
                'baseFee3' => $store->base_fee3,
                'baseWeight' => $store->base_weight,
                'addFeePerKg' => $store->addtional_fee_per_kg,
                'returnedFee' => $store->returned_fee,
                'refrigeratedFee' => $store->refrigerated_transport_fee,
                'exchangeFee' => $store->exchange_fee,
                'returnFee' => $store->return_fee,
                'codFee' => $store->cod_fee,
                'fixedAmountCodFee' => $store->fixed_amount_cod_fee,
                'codFeePercent' => $store->cod_fee_percent,
                'website' => $store->website,
                'bank' => $store->bankAccount()->exists() ? $store->bankAccount->bank_name : null,
                'accountOwner' =>  $store->bankAccount()->exists() ? $store->bankAccount->owner_name : null,
                'rib' => $store->bankAccount()->exists() ? $store->bankAccount->rib : null,
            ],
            'files' => $store->files,
            'cities' => City::where('type', 'register')->get() ?? [trans('alriyadh')],
            'addresses' => $store->pickupAddresses()->get(),
        ]);
    }

    public function update(Request $request, Store $store)
    {

        $this->authorize('isEmployeeHasPermission', 'edit_stores');
        if (auth()->user()->type != 'client')
            $request->validate(
                [
                    'ownerName' => 'required|string|max:255',
                    'name' => 'required|string|max:255',
                    'phone' => 'required|numeric||digits:10',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $store->user->id,
                    'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/u', 'unique:users,username,' . $store->user->id],
                    'id_number' => 'nullable|numeric|digits:10',
                ],
                []
            );


        if (auth()->user()->type != 'client'){
            $user = $store->user()->update([
                'name' => $request->ownerName,
                'email' => $request->email,
                'phone' => $request->phone,
                'username' => $request->username,
                'id_number' => $request->id_number,
            ]);

            $store = $store->update([
                'name' => $request->name,
                'website' => $request->website,
                'gov_number' => $request->govNumber,
                'freelance_document' => $request->freelanceDocument,
                'maaroof' => $request->maaroof,
                'tax_number' => $request->taxNumber,
                'cod_fee' => $request->codFee,
                'fixed_amount_cod_fee' => $request->fixedAmountCodFee,
                'cod_fee_percent' => $request->codFeePercent,
                'return_fee' => $request->returnFee,
                'city' => $request->city,
                'neighborhood' => $request->neighborhood,
                'base_fee' =>  $request->baseFee,
                'base_fee2' =>  $request->baseFee2,
                'base_fee3' =>  $request->baseFee3,
                'base_weight' =>  $request->baseWeight,
                'addtional_fee_per_kg' => $request->addFeePerKg,
                'returned_fee' =>  $request->returnedFee,
                'exchange_fee' =>  $request->exchangeFee,
                'refrigerated_transport_fee' =>  $request->refrigeratedFee,
            ]);
        } else {
            $store = $store->update([
                'name' => $request->name,
                'website' => $request->website,
                'gov_number' => $request->govNumber,
                'freelance_document' => $request->freelanceDocument,
                'maaroof' => $request->maaroof,
                'tax_number' => $request->taxNumber,
                'city' => $request->city,
                'neighborhood' => $request->neighborhood,
            ]);
        }


        return redirect::back()->with('success', trans('customer_data_updated_successfully'));
    }

    public function updateBankAccount(Request $request, Store $store)
    {

        $this->authorize('isEmployeeHasPermission', 'edit_stores');

        $request->validate(
            [
                'bank' => 'required|string|max:255',
                'accountOwner' => 'required|string|max:255',
                'rib' => 'required|string|max:255',
            ],
            [
                'accountOwner.required' => trans('Account_username_is_required'),
                'bank.required' => trans('bank_name_is_required'),
                'rib.required' => trans('account_number_is_required')
            ]
        );

        if ($store->bankAccount()->exists())
            $store->bankAccount()->update([
                'bank_name' => $request->bank,
                'owner_name' => $request->accountOwner,
                'rib' => $request->rib,
            ]);
        else
            $store->bankAccount()->create([
                'bank_name' => $request->bank,
                'owner_name' => $request->accountOwner,
                'rib' => $request->rib,
            ]);

        return redirect::back()->with('success', trans('bank_account_updated_successfully'));
    }

    public function dues(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'dues_stores');

        return Inertia::render('Stores/Dues', [
            'filters' => RequestFacade::all('search', 'status', 'year', 'month'),
            'stores' => Store::orderBy('id', 'desc')
                // ->where('dues', '!=', 0)
                ->when($request->search ?? null, function ($query, $search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('dues', 'like', '%' . $search . '%')
                        ->orWhereHas('bankAccount', function ($query) use ($search) {
                            $query->where('bank_name', 'like', '%' . $search . '%');
                        });
                })
                ->paginate(150)
                ->withQueryString()
                ->through(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'bank' => $store->bankAccount()->exists() ? $store->bankAccount->bank_name : null,
                    'dues' => getStoreDues($store),
                    'fees' => getStoreFees($store->id, [100, -100], 0, $request->year, $request->month),
                    'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->where('status', 100)
                                ->when($request->year ?? null, function ($query, $year) {
                                    $query->whereYear('updated_at', $year);
                                })->when($request->month ?? null, function ($query, $month) {
                                    $query->whereMonth('updated_at', $month);
                                })
                                ->where('store_id', $store->id);
                        })->sum('cod'),
                    'cash' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->where('status', 100)
                            ->when($request->year ?? null, function ($query, $year) {
                                $query->whereYear('updated_at', $year);
                            })->when($request->month ?? null, function ($query, $month) {
                                $query->whereMonth('updated_at', $month);
                            })
                            ->where('store_id', $store->id);
                        })->where('payment_method','cash')->sum('cod'),
                    'epayment' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->where('status', 100)
                            ->when($request->year ?? null, function ($query, $year) {
                                $query->whereYear('updated_at', $year);
                            })->when($request->month ?? null, function ($query, $month) {
                                $query->whereMonth('updated_at', $month);
                            })
                            ->where('store_id', $store->id);
                        })->where('payment_method','epayment')->sum('cod'),
                    'receipt' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('created_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('created_at', $month);
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '<', 0)
                        ->sum('amount'),
                    'payment' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('created_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('created_at', $month);
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '>', 0)
                        ->sum('amount'),
                    'previousDues' => $request->year || $request->month ? calculateStoreDues([
                        'fees' => calculateFees(Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->whereIn('status', [100, -100])
                                ->when($request->year &&  !$request->month, function ($query, $year) {
                                    $query->whereYear('updated_at','<', $year);
                                })->when($request->month && !$request->year, function ($query, $month) {
                                    $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                                })->when($request->month && $request->year, function ($query) use ($request) {
                                    $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                                })
                                ->where('store_id', $store->id);
                            })->select(
                                DB::raw("SUM(cod) as cod "),
                                DB::raw("SUM(base_fee) as base_fee"),
                                DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                                DB::raw("SUM(extra_fees) as extra_fees"),
                                DB::raw("SUM(exchange_fee) as exchange_fee"),
                                DB::raw("SUM(cod_fee) as cod_fee"),
                            )->first()),
                        'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                                $query->where('status', 100)
                                    ->when($request->year &&  !$request->month, function ($query, $year) {
                                        $query->whereYear('updated_at','<', $year);
                                    })->when($request->month && !$request->year, function ($query, $month) {
                                        $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                                    })->when($request->month && $request->year, function ($query) use ($request) {
                                        $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                                    })
                                    ->where('store_id', $store->id);
                            })->sum('cod'),
                        'receipt' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                            $query->whereYear('created_at','<', $year);
                            })->when($request->month && !$request->year, function ($query, $month) {
                                $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                            })->when($request->month && $request->year, function ($query) use ($request) {
                                $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                            })
                            ->where('belongsToStoreWallet', 0)
                            ->where('amount', '<', 0)
                            ->sum('amount'),
                        'payment' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                            $query->whereYear('created_at','<', $year);
                            })->when($request->month && !$request->year, function ($query, $month) {
                                $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                            })->when($request->month && $request->year, function ($query) use ($request) {
                                $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                            })
                            ->where('belongsToStoreWallet', 0)
                            ->where('amount', '>', 0)
                            ->sum('amount'),
                    ]) : 0  ,
                    'ownerName' => $store->user->name,
                    'phone' => $store->user->phone,
                ]),
            'whatsappMessage' => Setting::where('option', 'whatsapp_store_message')->exists() ? str_replace("\n", "%0a‎",  Setting::where('option', 'whatsapp_store_message')->first()->value) : '',
        ]);
    }

    public function duesPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'dues_stores');

        $stores = Store::orderBy('id', 'desc')
            // ->where('dues', '!=', 0)
            ->when($request->search ?? null, function ($query, $search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('dues', 'like', '%' . $search . '%')
                    ->orWhereHas('bankAccount', function ($query) use ($search) {
                        $query->where('bank_name', 'like', '%' . $search . '%');
                    });
            })

            ->get()
            ->map(fn ($store) => [
                'id' => $store->id,
                'name' => $store->name,
                'bank' => $store->bankAccount()->exists() ? $store->bankAccount->bank_name : null,
                'dues' => getStoreDues($store),
                'fees' => calculateFees(Financial::where('wallet', 0)
                ->whereHas('shipment', function ($query) use ($store, $request) {
                    $query->whereIn('status', [100, -100])
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                })->select(
                    DB::raw("SUM(cod) as cod "),
                    DB::raw("SUM(base_fee) as base_fee"),
                    DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                    DB::raw("SUM(extra_fees) as extra_fees"),
                    DB::raw("SUM(exchange_fee) as exchange_fee"),
                    DB::raw("SUM(cod_fee) as cod_fee"),
                )->first()),
                'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->where('status', 100)
                            ->when($request->year ?? null, function ($query, $year) {
                                $query->whereYear('updated_at', $year);
                            })->when($request->month ?? null, function ($query, $month) {
                                $query->whereMonth('updated_at', $month);
                            })
                            ->where('store_id', $store->id);
                    })->sum('cod'),
                'cash' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->where('status', 100)
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                    })->where('payment_method','cash')->sum('cod'),
                'epayment' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->where('status', 100)
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                    })->where('payment_method','epayment')->sum('cod'),
                'receipt' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                        $query->whereYear('created_at', $year);
                    })->when($request->month ?? null, function ($query, $month) {
                        $query->whereMonth('created_at', $month);
                    })
                    ->where('belongsToStoreWallet', 0)
                    ->where('amount', '<', 0)
                    ->sum('amount'),
                'payment' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                        $query->whereYear('created_at', $year);
                    })->when($request->month ?? null, function ($query, $month) {
                        $query->whereMonth('created_at', $month);
                    })
                    ->where('belongsToStoreWallet', 0)
                    ->where('amount', '>', 0)
                    ->sum('amount'),
                'previousDues' => $request->year || $request->month ? calculateStoreDues([
                    'fees' => calculateFees(Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->whereIn('status', [100, -100])
                            ->when($request->year &&  !$request->month, function ($query, $year) {
                                $query->whereYear('updated_at','<', $year);
                            })->when($request->month && !$request->year, function ($query, $month) {
                                $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                            })->when($request->month && $request->year, function ($query) use ($request) {
                                $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                            })
                            ->where('store_id', $store->id);
                        })->select(
                            DB::raw("SUM(cod) as cod "),
                            DB::raw("SUM(base_fee) as base_fee"),
                            DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                            DB::raw("SUM(extra_fees) as extra_fees"),
                            DB::raw("SUM(exchange_fee) as exchange_fee"),
                            DB::raw("SUM(cod_fee) as cod_fee"),
                        )->first()),
                    'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->where('status', 100)
                                ->when($request->year &&  !$request->month, function ($query, $year) {
                                    $query->whereYear('updated_at','<', $year);
                                })->when($request->month && !$request->year, function ($query, $month) {
                                    $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                                })->when($request->month && $request->year, function ($query) use ($request) {
                                    $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                                })
                                ->where('store_id', $store->id);
                        })->sum('cod'),
                    'receipt' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                        $query->whereYear('created_at','<', $year);
                        })->when($request->month && !$request->year, function ($query, $month) {
                            $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                        })->when($request->month && $request->year, function ($query) use ($request) {
                            $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '<', 0)
                        ->sum('amount'),
                    'payment' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                        $query->whereYear('created_at','<', $year);
                        })->when($request->month && !$request->year, function ($query, $month) {
                            $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                        })->when($request->month && $request->year, function ($query) use ($request) {
                            $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '>', 0)
                        ->sum('amount'),
                ]) : 0  ,
                'ownerName' => $store->user->name,
                'phone' => $store->user->phone,
            ]);

        $stores = array_filter($stores->toArray(), function ($store) use ($request){
            if($request->status == '<')
                return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) < 0;
            elseif ($request->status == '>')
                return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) > 0;
            elseif ($request->status == '!0')
                return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) != 0;
            else
                return ($store['previousDues'] != 0 || $store['cod'] != 0 || $store['fees'] != 0 || $store['payment'] != 0 ||  $store['receipt'] != 0) ;
        });

        $pdf = PDF::loadView('pdf/dues', compact('stores'), [], ['format'  => 'A4']);
        return $pdf->download('dues.pdf');
    }

    public function duesExcel(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'dues_stores');

        $stores = Store::orderBy('id', 'desc')
            // ->where('dues', '!=', 0)
            ->when($request->search ?? null, function ($query, $search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('dues', 'like', '%' . $search . '%')
                    ->orWhereHas('bankAccount', function ($query) use ($search) {
                        $query->where('bank_name', 'like', '%' . $search . '%');
                    });
            })

            ->get()
            ->map(fn ($store) => [
                'id' => $store->id,
                'name' => $store->name,
                'bank' => $store->bankAccount()->exists() ? $store->bankAccount->bank_name : null,
                'dues' => getStoreDues($store),
                'fees' => calculateFees(Financial::where('wallet', 0)
                ->whereHas('shipment', function ($query) use ($store, $request) {
                    $query->whereIn('status', [100, -100])
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                })->select(
                    DB::raw("SUM(cod) as cod "),
                    DB::raw("SUM(base_fee) as base_fee"),
                    DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                    DB::raw("SUM(extra_fees) as extra_fees"),
                    DB::raw("SUM(exchange_fee) as exchange_fee"),
                    DB::raw("SUM(cod_fee) as cod_fee"),
                )->first()),
                'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                    $query->where('status', 100)
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                })->sum('cod'),
                'cash' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->where('status', 100)
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                    })->where('payment_method','cash')->sum('cod'),
                'epayment' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->where('status', 100)
                        ->when($request->year ?? null, function ($query, $year) {
                            $query->whereYear('updated_at', $year);
                        })->when($request->month ?? null, function ($query, $month) {
                            $query->whereMonth('updated_at', $month);
                        })
                        ->where('store_id', $store->id);
                    })->where('payment_method','epayment')->sum('cod'),
                'receipt' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                        $query->whereYear('created_at', $year);
                    })->when($request->month ?? null, function ($query, $month) {
                        $query->whereMonth('created_at', $month);
                    })
                    ->where('belongsToStoreWallet', 0)
                    ->where('amount', '<', 0)
                    ->sum('amount'),
                'payment' => $store->user->vouchers()->when($request->year ?? null, function ($query, $year) {
                        $query->whereYear('created_at', $year);
                    })->when($request->month ?? null, function ($query, $month) {
                        $query->whereMonth('created_at', $month);
                    })
                    ->where('belongsToStoreWallet', 0)
                    ->where('amount', '>', 0)
                    ->sum('amount'),

                'previousDues' => $request->year || $request->month ? calculateStoreDues([
                    'fees' => calculateFees(Financial::whereHas('shipment', function ($query) use ($store, $request) {
                        $query->whereIn('status', [100, -100])
                            ->when($request->year &&  !$request->month, function ($query, $year) {
                                $query->whereYear('updated_at','<', $year);
                            })->when($request->month && !$request->year, function ($query, $month) {
                                $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                            })->when($request->month && $request->year, function ($query) use ($request) {
                                $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                            })
                            ->where('store_id', $store->id);
                        })->select(
                            DB::raw("SUM(cod) as cod "),
                            DB::raw("SUM(base_fee) as base_fee"),
                            DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
                            DB::raw("SUM(extra_fees) as extra_fees"),
                            DB::raw("SUM(exchange_fee) as exchange_fee"),
                            DB::raw("SUM(cod_fee) as cod_fee"),
                        )->first()),
                    'cod' => Financial::whereHas('shipment', function ($query) use ($store, $request) {
                            $query->where('status', 100)
                                ->when($request->year &&  !$request->month, function ($query, $year) {
                                    $query->whereYear('updated_at','<', $year);
                                })->when($request->month && !$request->year, function ($query, $month) {
                                    $query->where('updated_at', '<' ,date('Year'). '-' . $month . '-01');
                                })->when($request->month && $request->year, function ($query) use ($request) {
                                    $query->where('updated_at', '<' ,$request->year. '-' . $request->month . '-01');
                                })
                                ->where('store_id', $store->id);
                        })->sum('cod'),
                    'receipt' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                        $query->whereYear('created_at','<', $year);
                        })->when($request->month && !$request->year, function ($query, $month) {
                            $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                        })->when($request->month && $request->year, function ($query) use ($request) {
                            $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '<', 0)
                        ->sum('amount'),
                    'payment' => $store->user->vouchers()->when($request->year &&  !$request->month, function ($query, $year) {
                        $query->whereYear('created_at','<', $year);
                        })->when($request->month && !$request->year, function ($query, $month) {
                            $query->where('created_at', '<' ,date('Year'). '-' . $month . '-01');
                        })->when($request->month && $request->year, function ($query) use ($request) {
                            $query->where('created_at', '<' ,$request->year. '-' . $request->month . '-01');
                        })
                        ->where('belongsToStoreWallet', 0)
                        ->where('amount', '>', 0)
                        ->sum('amount'),
                ]) : 0  ,
                'ownerName' => $store->user->name,
                'phone' => $store->user->phone,
            ]);

            $stores = array_filter($stores->toArray(), function ($store) use ($request){
                if($request->status == '<')
                    return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) < 0;
                elseif ($request->status == '>')
                    return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) > 0;
                elseif ($request->status == '!0')
                    return $store['previousDues'] - ($store['cod'] - $store['fees'] - $store['payment'] - $store['receipt']) != 0;
                else
                    return ($store['previousDues'] != 0 || $store['cod'] != 0 || $store['fees'] != 0 || $store['payment'] != 0 ||  $store['receipt'] != 0) ;
            });

        return Excel::download(new DuesExport($stores), 'dues.xlsx');
    }

    public function shipments(Store $store, Request $request)
    {
        return Inertia::render('Shipments', [
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
            ],

            'filters' => RequestFacade::all('search', 'status'),
            'shipments' => $store->shipments()
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
                ->through(fn ($shipment) => [
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

    public function updateStatus(Request $request, Store $store)
    {

        $this->authorize('isEmployeeHasPermission', 'index_stores');

        if ($store->status != 'approved')
            $store->update(['status' => 'approved']);
        else
            $store->update(['status' => 'closed']);

        return redirect::back()->with('success', trans('store_status_updated_successully'));
    }

    public function show(Store $store, Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'show_stores');

        return Inertia::render('Stores/Show', [
            'store' => [
                'id' => $store->id,
                'user_id' => $store->user->id,
                'name' => $store->name,
                'type' => $store->type,
                'status' => $store->status,
                'refregirated_transport_active' =>  $store->refrigerated_transport_active,

                'exchange_active' =>  $store->exchange_active,
                'cod_active' =>  intval($store->cod_active),
                'return_active' =>  $store->return_active,
                'invoice_active' =>  $store->invoice_active,
                'order_id_active' =>  $store->order_id_active,
                'postpaid_active' =>  $store->postpaid_active,
                'auto_billing' =>  $store->auto_billing,
                'billing_period' =>  $store->billing_period,
                'wallet' => calculateStoreWallet($store)
            ],
            'stats' => [
                'cods' => Financial::whereHas('shipment', function ($query) use ($store) {
                    return $query->where('store_id', $store->id)->where('status', 100);
                })->sum('cod'),
                'fees' => getStoreFees($store->id),
                'paid' => Voucher::where('user_id', $store->user->id)->where('amount', '>', 0)->where('belongsToStoreWallet', 0)->sum('amount'),
                'rec' => Voucher::where('user_id', $store->user->id)->where('amount', '<', 0)->where('belongsToStoreWallet', 0)->sum('amount'),
                'dues' => getStoreDues($store),
            ],
            'filters' => RequestFacade::all('search', 'status'),
            'shipments' => Shipment::with('financial')
                ->where('store_id', $store->id)
                ->whereIn('status', [100, -100])
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
                })
                ->orderBy('created_at', 'desc')
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($shipment) => [
                    'id' => $shipment->id,
                    'number' => $shipment->number,
                    'shipping_date' =>  $shipment->updated_at->format('Y-m-d H:i:s') ,
                    'fees' => calculateFees($shipment->financial),
                    'cod' => $shipment->financial->cod + 0,
                    'due' => $shipment->status == 100 ? (calculateFees($shipment->financial)) - $shipment->financial->cod : calculateFees($shipment->financial),
                    'city' => $shipment->city,
                    'status' => $shipment->status,
                    'notice' => $shipment->latestNotice ? $shipment->latestNotice->notice : trans('there_is_no_notes'),
                ]),

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
            'wallets' => Wallet::all()
        ]);
    }

    public function updateFeature(Request $request, Store $store, $feature)
    {

        $this->authorize('isEmployeeHasPermission', 'show_stores');


        $store->update([$feature => DB::raw('NOT ' . $feature)]);

        return redirect::back()->with('success', trans('updated_successfully'));
    }

    public function updateBilling(Request $request, Store $store){
        $this->authorize('isEmployeeHasPermission', 'show_stores');
        $store->update(['billing_period' => $request->period]);
        return redirect::back()->with('success', trans('updated_successfully'));
    }

    public function summary(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'summary_stores');

        return Inertia::render('Stores/Summary', [
            'stores' => Store::all()
                ->map(fn ($store) => [
                    'value' => $store->id,
                    'label'  => $store->id . '- ' . $store->name,
                ])
        ]);
    }

    public function getSummary(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'summary_stores');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }

        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $prev = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->whereIn('status', [100, -100])
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->select(
            DB::raw("SUM(cod) as cod "),
            DB::raw("SUM(base_fee) as base_fee"),
            DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
            DB::raw("SUM(extra_fees) as extra_fees"),
            DB::raw("SUM(exchange_fee) as exchange_fee"),
            DB::raw("SUM(cod_fee) as cod_fee"),
        )->first();

        $cod = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->where('status', 100)
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->sum('cod');

        $prevFees =  calculateFees($prev) - $cod;

        $prevVouchers = Voucher::where('user_id', $store->user->id)->where('created_at', '<', $from)->sum('amount');

        $sum = $prevFees + $prevVouchers;

        $vouchers = Voucher::where('user_id', $store->user->id)
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->id,
                'voucherNumber' => $voucher->number,
                'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
            ]);

        $shipments = Shipment::whereIn('status', [100, -100])
            ->where('store_id', $store->id)
            ->whereBetween('updated_at', [$from, $to])
            ->get()
            ->map(fn ($shipment) => [
                'shipmentId' => $shipment->id,
                'number' => $shipment->number,
                'date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => calculateFees($shipment->financial),
                'cod' => $shipment->status == -100 ? 0 : $shipment->financial->cod + 0,
                'codFee' => $shipment->financial->cod_fee,
                'receiverPhone' => $shipment->receiver_phone,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status
            ]);

        $merged = $shipments->toBase()->merge($vouchers)->sortBy('date');

        return Inertia::render('Stores/Summary', [

            'items' => $merged->values()->all(),
            'prevSum' => $sum,

        ]);
    }

    public function summaryPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'summary_stores');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }


        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $prev = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->whereIn('status', [100, -100])
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->select(
            DB::raw("SUM(cod) as cod"),
            DB::raw("SUM(base_fee) as base_fee"),
            DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
            DB::raw("SUM(extra_fees) as extra_fees"),
            DB::raw("SUM(exchange_fee) as exchange_fee"),
        )->first();

        $cod = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->where('status', 100)
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->sum('cod');

        $prevFees =  calculateFees($prev) - $cod;

        $prevVouchers = Voucher::where('user_id', $store->user->id)->where('created_at', '<', $from)->sum('amount');

        $sum = $prevFees + $prevVouchers;

        $vouchers = Voucher::where('user_id', $store->user->id)
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->number,
                'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
            ]);

        $shipments = Shipment::whereIn('status', [100, -100])
            ->where('store_id', $store->id)
            ->whereBetween('updated_at', [$from, $to])
            ->get()
            ->map(fn ($shipment) => [
                'shipmentId' => $shipment->id,
                'number' => $shipment->number,
                'date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => calculateFees($shipment->financial),
                'cod' => $shipment->status == -100 ? 0 : $shipment->financial->cod + 0,
                'codFee' => $shipment->financial->cod_fee,
                'receiverPhone' => $shipment->receiver_phone,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status
            ]);

        $merged = $shipments->toBase()->merge($vouchers)->sortBy('date');



        $items = $merged->values()->all();

        $to = $request->to;
        $from = $request->from;
        $store = $request->store;

        $pdf = PDF::loadView('pdf/store-summary', compact('items', 'to', 'from', 'store', 'sum'), [], ['format'  => 'A4']);
        return $pdf->download('summary.pdf');
    }


    public function summaryExel(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'summary_stores');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }


        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $prev = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->whereIn('status', [100, -100])
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->select(
            DB::raw("SUM(cod) as cod"),
            DB::raw("SUM(base_fee) as base_fee"),
            DB::raw("SUM(refrigerated_transport_fee) as refrigerated_transport_fee"),
            DB::raw("SUM(extra_fees) as extra_fees"),
            DB::raw("SUM(exchange_fee) as exchange_fee"),
        )->first();

        $cod = Financial::whereHas('shipment', function ($query) use ($store, $from) {
            $query->where('status', 100)
                ->where('store_id', $store->id)
                ->where('updated_at', '<', $from);
        })->sum('cod');

        $prevFees =  calculateFees($prev) - $cod;

        $prevVouchers = Voucher::where('user_id', $store->user->id)->where('created_at', '<', $from)->sum('amount');

        $sum = $prevFees + $prevVouchers;

        $vouchers = Voucher::where('user_id', $store->user->id)
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->number,
                'date' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
            ]);

        $shipments = Shipment::whereIn('status', [100, -100])
            ->where('store_id', $store->id)
            ->whereBetween('updated_at', [$from, $to])
            ->get()
            ->map(fn ($shipment) => [
                'shipmentId' => $shipment->id,
                'number' => $shipment->number,
                'date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' => calculateFees($shipment->financial),
                'cod' => $shipment->status == -100 ? 0 : $shipment->financial->cod + 0,
                'codFee' => $shipment->financial->cod_fee,
                'receiverPhone' => $shipment->receiver_phone,
                'receiverName' => $shipment->receiver_name,
                'storeName' => $shipment->store->name,
                'city' => $shipment->city,
                'neighborhood' => $shipment->neighborhood,
                'warehouseLocation' => $shipment->warehouse_location,
                'status' => $shipment->status
            ]);

        $merged = $shipments->toBase()->merge($vouchers)->sortBy('date');



        $items = $merged->values()->all();

        $to = $request->to;
        $from = $request->from;
        $store = $request->store;
        return Excel::download(new storeSummaryExport($items, $sum), 'statement.xlsx');

    }



    public function invoice(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'invoice_stores');

        if (auth()->user()->store && !auth()->user()->store->invoice_active)
            abort(401);

        return Inertia::render('Stores/TaxInvoice', [
            'stores' => Store::where('invoice_active', 1)
                ->get()
                ->map(fn ($store) => [
                    'value' => $store->id,
                    'label'  => $store->id . '- ' . $store->name,
                ])
        ]);
    }

    public function getInvoice(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'invoice_stores');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }

        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        return Inertia::render('Stores/TaxInvoice', [

            'items' => $store->invoice_active ? $store->shipments->sortBy('updated_at')->values()->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to])->map(fn ($shipment) => [
                'id' => $shipment->id,
                'number' => $shipment->number,
                'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
                'fees' =>  calculateFees($shipment->financial),
                'cod' => $shipment->financial->cod + 0,
                'receiverPhone' => $shipment->receiver_phone,
                'status' => $shipment->status,
                'tax' => $shipment->financial->tax,
            ]) : [],

            'sum' => [
                'fees' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                    return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
                })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')),
                'tax' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                    return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
                })->sum('tax'),
                'cod' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                    return $query->where('store_id', $store->id)->whereIn('status', [100])->whereBetween('updated_at', [$from, $to]);
                })->sum('cod'),
            ]

        ]);
    }

    public function invoicePdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'invoice_stores');

        $request->validate([
            'to' => 'required',
            'from' => 'required',
        ]);

        if (Auth::user()->type == "client")
            $store = Auth::user()->store;
        else {
            $request->validate([
                'store' => 'required|exists:stores,id',
            ]);
            $store = Store::find($request->store);
        }

        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));

        $items = $store->invoice_active ? $store->shipments->sortBy('updated_at')->values()->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to])->map(fn ($shipment) => [
            'id' => $shipment->id,
            'number' => $shipment->number,
            'invoice' => str_pad($shipment->invoice_number, 10, '0', STR_PAD_LEFT),
            'shipping_date' => $shipment->updated_at->format('Y-m-d H:i:s'),
            'fees' => calculateFees($shipment->financial),
            'cod' => $shipment->financial->cod + 0,
            'receiverPhone' => $shipment->receiver_phone,
            'status' => $shipment->status,
            'tax' => $shipment->financial->tax,
        ]) : [];

        $sum = [
            'fees' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) ')),
            'tax' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100, -100])->whereBetween('updated_at', [$from, $to]);
            })->sum('tax'),
            'cod' => Financial::whereHas('shipment', function ($query) use ($store, $from, $to) {
                return $query->where('store_id', $store->id)->whereIn('status', [100])->whereBetween('updated_at', [$from, $to]);
            })->sum('cod'),
        ];

        $to = $request->to;
        $from = $request->from;

        $pdf = PDF::loadView('pdf/store-tax-invoice', compact('items', 'sum', 'to', 'from', 'store'), [], ['format'  => 'A4']);
        return $pdf->stream('tax-invoice.pdf');
    }

    public function setOperator(Request $request, Store $store, PickupAddress $address = null)
    {

        $this->authorize('isEmployeeHasPermission', 'receive_shipments');

        $request->validate(
            [
                'value' => 'required',
            ],
            [
                'value.required' => trans('required_field')
            ]
        );



        $operator = Operator::WhereHas('user', function ($query) use ($request) {
            $query->where('phone', $request->value);
        })->orWhere('identification', $request->value)->first();

        if ($operator) {

            if ($address){
                $address->update(['operator_id' => $operator->id]);
            }else {
                $store->update(['operator_id' => $operator->id]);
            }


            return redirect::back()->with('success', trans('delegate_attached'));
        } else
            return redirect::back()->with('error', trans('delegate_not_available'));
    }

    public function wallets(Request $request){
        $this->authorize('isEmployeeHasPermission', 'wallets_stores');


        $stores = Store::orderBy('id', 'desc')
                ->when($request->search ?? null, function ($query, $search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');
                })->get()
                ->map(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'type' => $store->type,
                    'postpaid' => $store->postpaid_active,
                    'ownerName' => $store->user->name,
                    'wallet' => calculateStoreWallet($store)
                ]);

        $stores = array_filter($stores->toArray(), function($value) use ($request) {
            if ($request->status == 'recharged')
                return $value['wallet']['balance'] > 0  ;
            elseif ($request->status == 'empty')
                return ($value['wallet']['fees'] > 0 || $value['wallet']['paid'] > 0 ||$value['wallet']['outstandingBalance'] > 0) && $value['wallet']['balance'] == 0  ;

            return $value['wallet']['fees'] > 0 || $value['wallet']['paid'] > 0 ||$value['wallet']['outstandingBalance'] > 0 ||$value['wallet']['balance'] > 0  ;
        });

        return Inertia::render('Stores/Wallets', [
            'filters' => RequestFacade::all('search', 'status'),
            'stores' => $stores
        ]);
    }

    public function walletsPdf(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'wallets_stores');

        $stores = Store::orderBy('id', 'desc')
                ->get()
                ->map(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'type' => $store->type,
                    'postpaid' => $store->postpaid_active,
                    'ownerName' => $store->user->name,
                    'wallet' => calculateStoreWallet($store)
                ]);

        $stores = array_filter($stores->toArray(), function($value) {
                    return $value['wallet']['fees'] > 0 || $value['wallet']['paid'] > 0 ||$value['wallet']['outstandingBalance'] > 0 ||$value['wallet']['balance'] > 0  ;
                });

        $pdf = PDF::loadView('pdf/stores-wallets', compact('stores'), [], ['format'  => 'A4']);
        return $pdf->download('stores-wallets.pdf');
    }

    public function walletsExcel()
    {
        $this->authorize('isEmployeeHasPermission', 'wallets_stores');

        $stores = Store::orderBy('id', 'desc')
                ->get()
                ->map(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'type' => $store->type,
                    'postpaid' => $store->postpaid_active,
                    'ownerName' => $store->user->name,
                    'wallet' => calculateStoreWallet($store)
                ]);

        $stores = array_filter($stores->toArray(), function($value) {
                    return $value['wallet']['fees'] > 0 || $value['wallet']['paid'] > 0 ||$value['wallet']['outstandingBalance'] > 0 ||$value['wallet']['balance'] > 0  ;
                });

        return Excel::download(new StoresWalletsExport($stores), 'stores-wallets.xlsx');
    }

    public function export()
    {
        $this->authorize('isEmployeeHasPermission', 'index_stores');

        $stores = Store::with('user')->orderBy('id', 'desc')
                ->get()
                ->map(fn ($store) => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'ownerName' => $store->user->name,
                    'phone' => $store->user->phone,
                ]);

        return Excel::download(new StoresExport ($stores), 'stores.xlsx');
    }
}