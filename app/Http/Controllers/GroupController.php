<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\GroupsDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class GroupController extends Controller
{

    protected $permissions;

    public function __construct()
    {
        $this->permissions = [

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
    }

    public function index(Request $request, GroupsDepartment $depart = null)
    {
        if ($request->for) {
            if ($request->for == 'rec') {
                $this->authorize('isEmployeeHasPermission', 'show_rec_operators');
            }

            if ($request->for == 'del') {
                $this->authorize('isEmployeeHasPermission', 'show_del_operators');
            }

            if ($request->for == 'vip') {
                $this->authorize('isEmployeeHasPermission', 'show_vip_operators');
            }

            $groupes = Groupe::where('for', $request->for)->get();
            $for = $request->for;
        } else {
            $this->authorize('isEmployeeHasPermission', 'show_employees');
            if (!$depart) {
                $groupes = Groupe::where('for', 'employees')->get();
            } else {
                $groupes = $depart->groups;
            }

            $for = null;
        }

        return Inertia::render('Employees/Groupes/Index', [
            'groupes' => $groupes->map(fn($groupe) => [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'count' => $groupe->users()->count(),
                'for' => $groupe->for,
            ]),
            'groupFor' => $for,
            'depart' => $depart,
        ]);

    }

    public function create(Request $request)
    {


        if ($request->for) {
            $for = $request->for;
        } else {
            $for = null;
        }

        if ($request->depart) {
            $depart = GroupsDepartment::find($request->depart);
        } else {
            $depart = null;
        }

        return Inertia::render('Employees/Groupes/Create', [
            'permissions' => $this->permissions,
            'groupFor' => $for,
            'depart' => $depart,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->for) {
            $for = $request->for;
        } else {
            $for = "employees";
        }

        if ($request->depart) {
            $depart = GroupsDepartment::find($request->depart)->id ?? null;
        } else {
            $depart = null;
        }

        $groupe = Groupe::create([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => json_encode($request->permissions),
            'for' => $for,
            'groups_department_id' => $depart,
        ]);

        return redirect::back()->with('success', trans('group_added_successfully'));
    }

    public function show($id)
    {
        //
    }

    public function edit(Groupe $groupe)
    {

        return Inertia::render('Employees/Groupes/Edit', [
            'groupe' => [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'description' => $groupe->description,
                'permissions' => json_decode($groupe->permissions),
                'for' => $groupe->for,
            ],
            'permissions' => $this->permissions,
        ]);
    }

    public function update(Request $request, Groupe $groupe)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $groupe->update([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => json_encode($request->permissions),
        ]);

        return redirect::back()->with('success', trans('group_updated_successfully'));
    }

    public function destroy($id)
    {
        //
    }
}
