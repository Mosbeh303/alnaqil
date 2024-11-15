<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleGroup;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->authorize('isEmployeeHasPermission', 'index_vehicles');

        return Inertia::render('Vehicles/Index', [
            'filters'   => Request::all('search', 'group'),
            'vehicles' => Vehicle::when(Request::input('search') ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('mark', 'like', '%'.$search.'%')
                            ->orWhere('plate_number', 'like', '%'.$search.'%')
                            ->orWhere('back_plate_number', 'like', '%'.$search.'%');
                    });
                })->when(Request::input('group') ?? null , function ($query, $group){
                    $query->WhereHas('vehicleGroup', function ($query) use ($group) {
                        $query->where('id', $group);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($vehicle) => [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                    'mark' => $vehicle->mark,
                    'plate_number' => $vehicle->plate_number,
                    'back_plate_number' => $vehicle->back_plate_number,
                    'user' => $vehicle->user->name  ?? null ,
                    'userType' => $vehicle->user->type  ?? null
                ]),
            'groups' => VehicleGroup::all()
                ->map(fn ($group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                ])


        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $this->authorize('isEmployeeHasPermission', 'create_vehicles');

        return Inertia::render('Vehicles/Create', [
            'groups' => VehicleGroup::all()
            ->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
            ]),
            'group' => Request::input('group') ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_vehicles');

        Request::validate([
            'name' => 'required|string|max:255',
            'group'  => 'required',
        ]);

        Vehicle::create([
            'name' => Request::input('name'),
            'mark' => Request::input('mark'),
            'plate_number' => Request::input('plateNumber'),
            'back_plate_number' => Request::input('backPlateNumber'),
            'vehicle_group_id' => Request::input('group') ,
        ]);

        return back()->with('success', trans('car_added_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_vehicles');

        return Inertia::render('Vehicles/Edit', [
            'groups' => VehicleGroup::all()
            ->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
            ]),
            'vehicle' => $vehicle,
            'user' => $vehicle->user ?? [] ,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_vehicles');

        Request::validate([
            'name' => 'required|string|max:255',
            'group'  => 'required',
        ]);

        $user_id =  User::where('phone', Request::input('phone') )->where('type', Request::input('user_type'))->first()->id ?? null;

        if (!$user_id){
            return back()->with('error', trans('employee_representative_mobile_number_does_not_exist'));
        }

        $vehicle->update([
            'name' => Request::input('name'),
            'mark' => Request::input('mark'),
            'plate_number' => Request::input('plateNumber'),
            'back_plate_number' => Request::input('backPlateNumber'),
            'vehicle_group_id' => Request::input('group') ,
            'user_id' => $user_id
        ]);

        return back()->with('success', trans('car_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        //
    }
}
