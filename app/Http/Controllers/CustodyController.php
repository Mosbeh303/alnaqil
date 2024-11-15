<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB ;
use App\Models\User ;
use App\Models\Custody ;

class CustodyController extends Controller
{
    public function store(Request $request, User $user)
    {
        //! $this->authorize('isEmployeeHasPermission', 'show_operators');

        $request->validate([
            'amount' => 'required|numeric',
        ],
        [
            'amount.required' => trans('amount_required'),
        ]);

        $max = DB::table('custodies')->max('number') + 1;

        $custody = $user->custodies()->create([
            'number' => $max,
            'amount' => $request->amount,
            'notice' => $request->notice,
        ]);

        return back()->with('success', trans('Custody_added_successfully'));

    }

    public function update(Request $request, Custody $custody)
    {
        //! $this->authorize('isEmployeeHasPermission', 'show_operators');

        $request->validate([
            'amount' => 'required|numeric',
        ],
        [
            'amount.required' => trans('amount_required'),
        ]);

        $custody->update([
            'amount' => $request->amount,
            'notice' => $request->notice,
        ]);

        return back()->with('success', trans('Custody_added_successfully'));

    }


    public function destroy(Custody $custody){
        $custody->delete();
        return back()->with('success',trans('Custody_deleted_successfully'));
    }
}
