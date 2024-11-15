<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use Illuminate\Support\Facades\Redirect;


class DistrictController extends Controller
{
    public function store(Request $request)
    {
        //$this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'value' => 'required|string|max:255',
        ],
        [
            'value.required' => trans('required_field'),
        ]);

        $data = District::create([
            'name' => $request->value,
            // 'type' => 'shipping_cities'
        ]);


        return Redirect::back()->with('success', trans('cityAdded'));
    }

    public function destroy(District $district)
    {
        //$this->authorize('isEmployeeHasPermission', 'settings');
        $district->delete();
        return redirect::back()->with('success', trans('city_deleted_successfully'));
    }

}
