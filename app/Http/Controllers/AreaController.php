<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Area;
use Inertia\Inertia;
use Redirect;

class AreaController extends Controller
{
    public function index(City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Areas/Index', [
            'areas' => $city->areas->map(fn ($area) => [
                'id' => $area->id,
                'name' => $area->name,
                'neighborhoods' => $area->neighborhoods ? $area->neighborhoods  : [],
            ]),
            'city'  => $city
        ]);
    }

    public function store(Request $request, City $city)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $data = $city->areas()->create([
            'name' => $request->name,
        ]);


        return back()->with('success', trans('cityAdded'));
    }

    public function update(Request $request, Area $area)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'name.required' => trans('required_field'),
        ]);

        $area->update([
            'name' => $request->name,
        ]);

        return back()->with('success', trans('city_updated'));
    }

    public function destroy(Area $area)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $area->delete();
        return back()->with('success', trans('city_deleted_successfully'));
    }

}
