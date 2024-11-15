<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Groupe;
use App\Models\City;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request AS RequestFacade;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'show_employees');
        $permissions = [

            'dashboard' => [
                'show_shipments_stats' => trans('Displaying_shipments_stats'),
                'show_clients_stats' => trans('display_job_stats'),
                'show_operators_stats' => trans('display_delegates_stats'),
                'show_sms_stats' => trans('display_sms_stats'),
                'show_profit_stats' => trans('display_profits_stats'),
                'show_latest_clients' => trans('display_latest_clients'),
                'show_latest_shipments' => trans('display_latest_shipments'),
            ],
            'shipments' => [
                'create_shipments' => trans('add_a_new_shipment'),
                'edit_shipments' => trans('shipment_modification'),
                'show_shipments' => trans(('display_shipment_details')),
                'index_shipments' => trans('display_active_shipments'),
                'query_shipments' => trans('Shipment_inquiry'),
                'track_shipments' => trans('Track_shipments'),

                'failed_shipments' => trans('Unsuccessful_delivery_attempts'),
                'receive_shipments' => trans('receiving_shipments'),
                'warehouse_shipments' => trans('add_storage_location'),
                'status_shipments' => trans('modify_the_shipment_status'),
                'operator_shipments' => trans('attach_employe_to_the_shipment'),
                'fee_shipments' => trans('delivery_fee'),
                'add_notices_shipments' => trans('Writing_notes_on_shipment'),
                'add_ADP' => trans('add_ADP'),
                'shipments_jt_create_order' => trans('issuing_jt_waybill'),


            ],
            'operators' => [
                'show_rec_operators' => trans('Show_receipts'),
                'show_del_operators' => trans('View_delivery_handlers'),
                'show_vip_operators' => trans('diplaying_featured_handlers'),
                'create_operators' => trans('add_delegate'),
                'edit_operators' => trans('updating_delegates'),
                'show_operators' => trans('show_delegates_detail'),
                'index_vehicles' => trans('Car_receipts'),
                'edit_vehicles' => trans('car_update'),
                'create_vehicles' => trans('add_a_new_car'),
                'create_voucher_operators' => trans('create_voucher_operators'),
                'edit_voucher_operators' => trans('edit_voucher_operators'),
                'create_custody_operators' => trans('create_custody_operators'),
                'edit_custody_operators' => trans('edit_custody_operators'),
            ],
            'employees' => [
                'show_employees' => trans('show_employees'),
                'create_employees' => trans('create_employees'),
                'edit_employees' => trans('edit_employees'),


            ],
            'stores' => [
                'index_stores' => trans('index_stores'),
                'edit_stores' => trans('edit_store'),
                'show_stores' => trans('show_store'),
                'dues_stores' => trans('dues_stores'),
                'wallets_stores' => trans('clients_wallets'),
                'create_stores' => trans('create_stores'),
                'summary_stores' => trans('summary_stores'),
                'vouchers_stores' => trans('vouchers_stores'),
                'receivers_stores' => trans('receivers_adresses'),
                'invoices_stores' => trans('invoices_stores'),
            ],

            'reports' => [
                'summary_shipments' => trans('Shipment_statement'),
                'accounting_statement_shipments' => trans('Accounting_shipment_statement'),
                'jnt_summary_shipments' => trans('summary_jnt'),
                'invoice_stores' => trans('invoice_stores'),
                'financials' => trans('Finance'),
            ],

            'accounts' => [
                'index_stores' => trans('index_stores'),
                'edit_voucher_accounts' => trans('edit_voucher_accounts'),
                'add_voucher_accounts' => trans('add_voucher_accounts'),
                'receipt_operator' => trans('receipt_operator'),
                'receipt_employees_dues_accounts' => trans('receipt_employees_dues_accounts'),
            ],
            'marketers' => [
                'show_marketers' => trans('show_marketers'),
                'create_marketers' => trans('create_marketers'),
                'edit_marketers' => trans('edit_marketers'),

            ],
            'others' => [

                'partners' => trans('Partners'),
                'settings' => trans('settings'),
                'complaints' => trans('complaints'),



            ],
        ];

        return Inertia::render('Employees/Index', [
            'permissions' => $permissions,
            'filters'   => RequestFacade::all('search', 'groupe'),
            'employees' => User::when($request->search ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('username', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhereHas('employee', function ($query) use ($search) {
                                $query->whereHas('city', function ($query) use ($search) {
                                    $query->where('name', 'like', '%'.$search.'%');
                                })->orWhereHas('branch', function ($query) use ($search) {
                                    $query->where('name', 'like', '%'.$search.'%');
                                });;
                            });
                    });
                })->when($request->groupe ?? null , function ($query, $groupe){
                    $query->WhereHas('groupe', function ($query) use ($groupe) {
                        $query->where('id', $groupe);
                    });
                })
                ->where('type', 'employee')
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'groupe' => $user->groupe->name  ?? null ,
                    'branch' => $user->employee->branch->name  ?? null ,
                    'city' => $user->employee->city->name  ?? null ,
                    'permissions' => json_decode($user->employee->permissions)  ?? null
                ]),
            'groupes' => Groupe::where('for', 'employees')
                ->get()
                ->map(fn ($groupe) => [
                    'id' => $groupe->id,
                    'name' => $groupe->name,
                ]),

            'group' => [
                'id' => $request->groupe ?? null,
                'permissions' => json_decode(Groupe::find($request->groupe)->permissions ?? ""),
            ] ,


        ]);
    }


    public function create(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_employees');
        return Inertia::render('Employees/Create', [
            'groupes' => Groupe::where('for', 'employees')
            ->get()
            ->map(fn ($groupe) => [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'employees' => $groupe->users()->count(),
            ]),
            'groupe' => $request->groupe ,
            'cities' => City::where('type' , 'register')->get() ?? [trans('alriyadh')],
        ]);
    }


    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_employees');
        $request->validate([
            'name' => 'required|string|max:255',
            'groupe'  => 'required',
            'phone' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('type', 'employee');
            })],
            'email' => 'required|string|email|max:255|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^\S*$/u'],
            'password' => ['required', Rules\Password::min(6)],
            'id_number' => ['numeric','unique:employees'],
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
            'type' => 'employee',
            'groupe_id' => $request->groupe
        ]);

        $user->employee()->create([
            'dues' => 0,
            'city_id' => $request->city,
            'branch_id' => $request->branch,
            'neighborhood' => $request->neighborhood,
            'id_number' => $request->id_number,
        ]);

        event(new Registered($user));

        return redirect::back()->with('success', trans('employe_added_successfully'));

    }


    public function updatePermissions(User $employee, Request $request)
    {
        $employee->employee->update([
            'permissions' => json_encode($request->permissions)
        ]);

        return redirect::back()->with('success', trans('employe_data_updated_successfully'));
    }


    public function edit(User $employee)
    {

        $this->authorize('isEmployeeHasPermission', 'edit_employees');

        return Inertia::render('Employees/Edit', [
            'employee' => [
                'id' => $employee->id,
                'username' => $employee->username,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'email' => $employee->email,
                'groupe_id' => $employee->groupe_id,
                'city_id' => $employee->employee->city_id,
                'branch_id' => $employee->employee->branch_id,
                'id_number' => $employee->employee->id_number,
            ],
            'groupes' => Groupe::all()
                ->map(fn ($groupe) => [
                    'id' => $groupe->id,
                    'name' => $groupe->name,
                    'employees' => $groupe->users()->count(),
                ]),
            'cities' => City::where('type' , 'register')->get() ?? [trans('alriyadh')],
            'branchesProps' => $employee->employee->city->branches ?? null
    ]);
    }


    public function update(Request $request, User $employee)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_employees');
        $request->validate([
            'name' => 'required|string|max:255',
            'groupe'  => 'required',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|string|email|max:255|unique:users,email,'.$employee->id,
            'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/u', 'unique:users,username,'.$employee->id ],
            'id_number' => 'numeric|unique:employees,id_number,'.$employee->employee->id,
        ],
        [

        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->username,
            'groupe_id' => $request->groupe,

        ]);

        $employee->employee->update([
            'city_id' => $request->city,
            'branch_id' => $request->branch,
            'id_number' => $request->id_number,
        ]);

        return redirect::back()->with('success', trans('employe_data_updated_successfully'));
    }

    public function updatePassword(Request $request, User $employee){
        $request->validate([
            'password' => ['required', Rules\Password::min(6)],
        ]);

        $employee->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect::back()->with('success', trans('employe_password_updated_successfully'));
    }




    public function destroy($id)
    {
        //
    }
}
