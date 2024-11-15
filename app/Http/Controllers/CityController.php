<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Models\City;
use App\Models\District;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CityExport;


class CityController extends Controller
{

    public function show(City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Cities/Show', [
            'city'  => $city,
            'branches' => $city->branches->map(fn ($branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
            ]),
        ]);
    }


    public function store(Request $request, District $district)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $data = $district->cities()->create([
            'name' => $request->name,
            'name_en' => $request->nameEn,
            'code' => $request->code,
            'active' => $request->active ? 1 : 0,
            'type' => 'shipping_cities',
            'fee_range' => $request->feeRange
        ]);


        return Redirect::back()->with('success', trans('city_added_successfully'));
    }

    public function storeRegisterCity(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'value' => 'required|string|max:255',
        ],
        [
            'value.required' => trans('required_field'),
        ]);

        if (City::where('name', $request->value)->where('type', 'register')->exists())
            return Redirect::back()->with('error', trans('city_name_exist'));

        $data = City::create([
            'name' => $request->value,
            'type' => 'register'
        ]);


        return Redirect::back()->with('success', trans('cityAdded'));
    }

    public function update(Request $request, City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $data = $city->update([
            'name' => $request->name,
            'name_en' => $request->nameEn,
            'code' => $request->code,
            'active' => $request->active ? 1 : 0,
            'fee_range' => $request->feeRange
        ]);


        return Redirect::back()->with('success', trans('city_updated'));
    }

    public function destroy(City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $city->delete();
        return redirect::back()->with('success', trans('city_deleted_successfully'));
    }

    public function export($district = null)
    {
        if ($district)
         $cities = City::where('district_id', $district)->get();
        else
         $cities = City::all();

         return Excel::download(new CityExport($cities), 'cities.xlsx');
    }
}
