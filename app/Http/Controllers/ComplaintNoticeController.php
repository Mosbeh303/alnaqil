<?php

namespace App\Http\Controllers;

use App\Models\ComplaintNotice;
use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintNoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Complaint $complaint)
    {
        $request->validate([
            'content' => 'required',
        ],
        [
            'content.required' => trans('required_field')
        ]);


        $complaint->complaintNotices()->create([
            'user_id' => auth()->user()->id,
            'content' => $request->content,
        ]);

        return back()->with('success', trans('refinement_added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComplaintNotice  $complaintNotice
     * @return \Illuminate\Http\Response
     */
    public function show(ComplaintNotice $complaintNotice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComplaintNotice  $complaintNotice
     * @return \Illuminate\Http\Response
     */
    public function edit(ComplaintNotice $complaintNotice)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComplaintNotice  $complaintNotice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComplaintNotice $notice)
    {
        $request->validate([
            'content' => 'required',
        ],
        [
            'content.required' => trans('required_field')
        ]);


        $notice->update([
            'content' => $request->content,
        ]);

        return back()->with('success', trans('refinement_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComplaintNotice  $complaintNotice
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComplaintNotice $complaintNotice)
    {
        //
    }
}
