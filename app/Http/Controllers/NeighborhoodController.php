<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Neighborhood;
use App\Models\City;
use Redirect;

class NeighborhoodController extends Controller
{
    public function store(Request $request, Area $area)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');

        $request->validate([
            'name' => 'required|string|max:255',
        ],
        [
            'value.required' => trans('required_field'),
        ]);

        $data = $area->neighborhoods()->create([
            'name' => $request->name,
        ]);


        return back()->with('success', trans('neighborhood_added_successfully'));
    }



    public function destroy(Neighborhood $neighborhood)
    {
        $this->authorize('isEmployeeHasPermission', 'settings');
        $neighborhood->delete();
        return back()->with('success', trans('city_deleted_successfully'));
    }

    public function getNeighborhoods($city){
        try{
            $city = City::where('name', $city)->with('areas', 'areas.neighborhoods')->first();

            $data = [];

            if ($city){
                $areas   = $city->areas;
                $neighborhoods = [];

                foreach($areas as $area){
                    array_push($neighborhoods, $area->neighborhoods);
                }

                $data = [];

                foreach($neighborhoods as $key => $value){
                    array_push($data,$value[0]->name);
                }
            }



            return response()->json($data);
        }catch (\Exception $e) {

        }

    }
}
