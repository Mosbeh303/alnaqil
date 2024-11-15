<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Partner;
use Redirect;


class PartnerController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        return Inertia::render('Partners/Index', [
            'partners' => Partner::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        return Inertia::render('Partners/Create', );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|mimes:png,jpeg,jpg|max:2048',
            'website' => 'url|nullable'
        ],
        [

        ]);

        if ($request->logo) {
            $fileName = time().'.'.$request->logo->extension();

            $request->logo->move(public_path('uploads'), $fileName);
            //dd($request->logo);
        }

        $data = Partner::create([
            'name' => $request->name,
            'website' => $request->website,
            'logo' => $fileName,
        ]);


        return Redirect::back()->with('success', trans('Partner_added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Partner $partner)
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        return Inertia::render('Partners/Edit', [
            'partner' => $partner,
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Partner $partner)
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'url|nullable'
        ],
        [

        ]);

        if ($request->logo) {
            $request->validate([
                'logo' => 'mimes:png,jpeg,jpg|max:2048',
            ]);
            $fileName = time().'.'.$request->logo->extension();
            $request->logo->move(public_path('uploads'), $fileName);
            $partner->update([
                'logo' => $fileName,
            ]);
        }

        $partner->update([
            'name' => $request->name,
            'website' => $request->website,
        ]);

        return Redirect::route('partners.index')->with('success', trans('Partner_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partner $partner)
    {
        $this->authorize('isEmployeeHasPermission', 'partners');
        $partner->delete();
        return redirect::back()->with('success', trans('Partner_deleted_successfully'));
    }
}
