<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Branch;


class BranchController extends Controller
{
    public function store(Request $request, City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $data = $city->branches()->create([
            'name' => $request->name,
        ]);


        return back()->with('success', trans('Branch_added_successfully'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $branch->update([
            'name' => $request->name,
        ]);

        return back()->with('success', trans('branch_updated_successfully'));
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $branch->delete();
        return back()->with('success', trans('branch_deleted_successfully'));
    }

    public function getBranches(City $city){

        $data = [];

        $data   = $city->branches;

        return response()->json($data);
    }

}
