<?php

namespace App\Http\Controllers;

use App\Models\EmpReimbursement;
use App\Models\Groupe;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;
use Redirect;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeExport;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;


class EmpReimbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $groupes =  Groupe::where('for', 'employees')->whereJsonContains('permissions', 'receipt_operator')->get();

        return Inertia::render('Reimbursements/Index', [
            'groupes' => $groupes->map(fn ($groupe) => [
                'id' => $groupe->id,
                'name' => $groupe->name,
                'count' => $groupe->users()->count(),
                'for' => $groupe->for
            ]),
        ]);
    }

    public function groupe(Groupe $groupe)
    {

        if ($groupe->for !== 'employees' && in_array('receipt_operator', json_decode($groupe->permissions)))
            abort(404);

        return Inertia::render('Reimbursements/Groupe', [
            'employees' => $groupe->users()->get()->map(fn ($user) => [
                'id' => $user->employee->id,
                'name' => $user->name,
                'username' => $user->username,
                'dues'   => getEmployeeDue($user->employee) ?? 0,
            ]),
            'groupe' => [
                'id' => $groupe->id,
                'name' => $groupe->name
            ]
        ]);
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
    public function store(Request $request, Employee $employee)
    {
        $request->validate(
            [
                'amount' => 'required|numeric|gt:0',
            ],
            [
                'amount.required' => trans('amount_required')
            ]
        );

        $max = DB::table('emp_reimbursements')->max('number') + 1;

        $employee->reimbursements()->create([
            'user_id' => auth()->user()->id,
            'number' => $max,
            'amount' => $request->amount,
            'notice' => $request->notice,
        ]);

        $employee->decrement('dues', $request->amount);

        return redirect::back()->with('success', trans('redemption_added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmpReimbursement  $empReimbursement
     * @return \Illuminate\Http\Response
     */
    public function show(EmpReimbursement $empReimbursement, Request $request)
    {

        //! $this->authorize('isEmployeeHasPermission', 'show_operators');

        return Inertia::render('Vouchers/Show', [
            'voucher' => [
                'id' => $empReimbursement->id,
                'number' => $empReimbursement->number,
                'to_bank' =>  trans('cash'),
                'bank' =>  trans('cash'),
                'amount' => $empReimbursement->amount,
                'name' => $empReimbursement->employee->user->name ?? null,
                'notice' => $empReimbursement->notice,
                'date' => $empReimbursement->created_at->format('Y/m/d'),
                'type' => 'empReimbursement'
            ],

        ]);
    }



    public function export(Employee $employee, Request $request)
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
        $sum = $employee->vouchers()->where('created_at' , '<' ,$from )->sum('amount');


        $vouchers = $employee->vouchers()
            ->where('type', '=', 'operator')
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->id,
                'number' => 'M' . $voucher->number,
                'created_at' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
                'operator' => $voucher->user->name,
                'responsible' => null,
                'type' => 'استلام'
            ]);

        $reimboursement = $employee->reimbursements()
            ->get()
            ->map(fn ($reimboursement) => [
                'id' => $reimboursement->id,
                'number' => 'MA' . $reimboursement->number,
                'created_at' => $reimboursement->created_at->format('Y-m-d H:i:s'),
                'amount' => $reimboursement->amount,
                'notice' => $reimboursement->notice,
                'operator' => null,
                'responsible' => $reimboursement->user->name ?? null,
                'type' => 'تسليم'
            ]);

        $merged = $vouchers->toBase()->merge($reimboursement)->sortByDesc('created_at');
        $vouchers = $merged->values()->whereBetween('created_at', [$from, $to])->all();

        foreach($vouchers as &$voucher){
            $sum = $sum - $voucher['amount'];

            $voucher['sum'] = $sum;

        }

        // $vouchers->sortByDesc('id')->values()->all();
        $vouchers = array_reverse($vouchers);

         return Excel::download(new EmployeeExport($vouchers), 'employees.xlsx');
    }


    public function pdf(Employee $employee, Request $request)
    {
        if ($request->from || $request->to) {
            $request->validate(
                [
                    'from' => 'required',
                    'to' => 'required',
                ],
                [
                    'from.required' => trans('required_field'),
                    'to.required' => trans('required_field'),
                ]
            );
        }

        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to . "+1 day"));
        $sum = $employee->vouchers()->where('created_at', '<', $from)->sum('amount');


        $vouchers = $employee->vouchers()
            ->where('type', '=', 'operator')
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->id,
                'number' => 'M' . $voucher->number,
                'created_at' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
                'operator' => $voucher->user->name,
                'responsible' => null,
                'type' => 'استلام'
            ]);

        $reimboursement = $employee->reimbursements()
            ->get()
            ->map(fn ($reimboursement) => [
                'id' => $reimboursement->id,
                'number' => 'MA' . $reimboursement->number,
                'created_at' => $reimboursement->created_at->format('Y-m-d H:i:s'),
                'amount' => $reimboursement->amount,
                'notice' => $reimboursement->notice,
                'operator' => null,
                'responsible' => $reimboursement->user->name ?? null,
                'type' => 'تسليم'
            ]);

        $merged = $vouchers->toBase()->merge($reimboursement)->sortByDesc('created_at');
        $vouchers = $merged->values()->whereBetween('created_at', [$from, $to])->all();

        foreach ($vouchers as &$voucher) {
            $sum = $sum - $voucher['amount'];

            $voucher['sum'] = $sum;
        }

        // $vouchers->sortByDesc('id')->values()->all();
        //$vouchers = array_reverse($vouchers);

        $to = date('Y-m-d', strtotime($request->to));
        $pdf = PDF::loadView('pdf/employee', compact('vouchers', 'employee', 'from', 'to'), [], ['format'  => 'A4', 'mode' => 'landscape']);
        return $pdf->download( $employee->user->name . '.pdf');

    }



    public function empReimbursements(Employee $employee, Request $request)
    {
        $vouchers = $employee->vouchers()
            ->where('type', '=', 'operator')
            ->get()
            ->map(fn ($voucher) => [
                'id' => $voucher->id,
                'number' => 'M' . $voucher->number,
                'created_at' => $voucher->created_at->format('Y-m-d H:i:s'),
                'amount' => $voucher->amount,
                'notice' => $voucher->notice,
                'operator' => $voucher->user->name,
                'responsible' => null,
                'type' => 'استلام'
            ]);

        $reimboursement = $employee->reimbursements()
            ->get()
            ->map(fn ($reimboursement) => [
                'id' => $reimboursement->id,
                'number' => 'MA' . $reimboursement->number,
                'created_at' => $reimboursement->created_at->format('Y-m-d H:i:s'),
                'amount' => $reimboursement->amount,
                'notice' => $reimboursement->notice,
                'operator' => null,
                'responsible' => $reimboursement->user->name ?? null,
                'type' => 'تسليم'
            ]);



        $merged = $vouchers->toBase()->merge($reimboursement)->sortByDesc('created_at');



        if ($request->from && $request->to) {
            $request->validate(
                [
                    'from' => 'required',
                    'to' => 'required',
                ],
                [
                    'from.required' => trans('required_field'),
                    'to.required' => trans('required_field'),
                ]
            );

            $from = date('Y-m-d', strtotime($request->from));
            $to = date('Y-m-d', strtotime($request->to . "+1 day"));
            //    $sum = $wallet->vouchers()->where('created_at' , '<' ,$from )->sum('amount');
            return Inertia::render('Reimbursements/Show', [
                'to' => $request->to,
                'from' => $request->from,
                'vouchers' => $merged->values()->whereBetween('created_at', [$from, $to])->all(),
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->user->name,
                    'dues' => getEmployeeDue($employee)
                ]
            ]);
        }


        return Inertia::render('Reimbursements/Show', [
            'to' => null,
            'from' => null,
            'vouchers' => $merged->values()->all(),
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->user->name,
                'dues' => getEmployeeDue($employee)
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmpReimbursement  $empReimbursement
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpReimbursement $empReimbursement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmpReimbursement  $empReimbursement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpReimbursement $empReimbursement)
    {
        $request->validate(
            [
                'amount' => 'required|numeric|gt:0',
            ],
            [
                'amount.required' => trans('amount_required')
            ]
        );

        $diff = $empReimbursement->amount - $request->amount;
        $empReimbursement->employee->decrement('dues', $diff);

        $empReimbursement->update([
            'amount' => $request->amount,
            'notice' => $request->notice,
        ]);

        return redirect::back()->with('success', trans('updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmpReimbursement  $empReimbursement
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpReimbursement $empReimbursement)
    {

        $empReimbursement->employee->increment('dues', $empReimbursement->amount);
        $empReimbursement->delete();

        return redirect::back()->with('success', trans('deleted_successfully'));
    }
}
