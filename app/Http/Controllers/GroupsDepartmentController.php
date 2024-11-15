<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\GroupsDepartment;
use Illuminate\Support\Facades\Redirect;

class GroupsDepartmentController extends Controller
{

    public function index()
    {
        return Inertia::render('Employees/Departments/Index', [
            'departs' => GroupsDepartment::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Employees/Departments/Create', []);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $depart = GroupsDepartment::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect::back()->with('success',trans('section_added_successfully'));
    }


    public function show($id)
    {
        //
    }


    public function edit(GroupsDepartment $depart)
    {
        return Inertia::render('Employees/Departments/Edit', [
            'depart' => [
                    'id' => $depart->id,
                    'name' => $depart->name,
                    'description' => $depart->description,
            ],
        ]);
    }


    public function update(Request $request,GroupsDepartment $depart)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $depart->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect::back()->with('success', trans('section_updated_successfully'));
    }


    public function destroy($id)
    {
        //
    }
}

