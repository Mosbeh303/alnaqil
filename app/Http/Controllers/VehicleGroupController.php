<?php

namespace App\Http\Controllers;

use App\Models\VehicleGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;


class VehicleGroupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'index_vehicles');
        return Inertia::render('Vehicles/Groups/Index', [
            'groups' => VehicleGroup::all()->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'count' => $group->vehicles()->count(),
            ]),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_vehicles');
        return Inertia::render('Vehicles/Groups/Create');
    }


    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'create_vehicles');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);



        $groupe = VehicleGroup::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('vehicles.groups')->with('success', trans('group_added_successfully'));
    }


    public function show($id)
    {
        //
    }


    public function edit(VehicleGroup $group)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_vehicles');
        return Inertia::render('Vehicles/Groups/Edit', [
            'group' => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
            ],
        ]);
    }


    public function update(Request $request,VehicleGroup $group)
    {
        $this->authorize('isEmployeeHasPermission', 'edit_vehicles');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => json_encode($request->permissions),
        ]);

        return back()->with('success', trans('group_updated_successfully'));
    }
}
