<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Complaint;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS RequestFacade;
use Carbon\Carbon;
use Auth;
use App\Integrations\Torood\ComplaintHelper;

class ComplaintController extends Controller
{

    public function index()
    {
        $this->authorize('isEmployeeHasPermission', 'complaints');



        return Inertia::render('Complaints/Index', [
            'filters' => RequestFacade::all('search', 'status'),
            'complaints' => Complaint::orderBy('created_at', 'desc')
                ->filter(RequestFacade::only('search', 'status'))
                ->paginate(50)
                ->withQueryString()
                ->through(fn ($complaint) => [
                    'id' => $complaint->id,
                    'number' => $complaint->id,
                    'user' => $complaint->user,
                    'complainantName' => $complaint->complainant_name,
                    'store' => $complaint->shipment->store->name,
                    'status'   => $complaint->status,
                    'subject'   => $complaint->subject,
                    'shipment'     => $complaint->shipment->number ,
                    'case'     => !$complaint->status ?  $complaint->case : null ,
                ]),
        ]);
    }


    public function create()
    {
        $this->authorize('isEmployeeHasPermission', 'complaints');
        return Inertia::render('Complaints/Create');
    }



    public function store(Request $request)
    {

        $request->validate([
            'shipment' => 'required|exists:App\Models\Shipment,number',
        ],
        [
            'shipment.exists' => trans('shipment_number_does_not_exist')
        ]);

        $shipment = Shipment::where('number', $request->shipment)->firstOrFail();



        $complaint = $shipment->complaints()->create([
            'user_id' => auth()->user()->id,
            'complainant_name' => $request->complainant,
            'subject' => $request->subject,
        ]);

        $complaint->complaintNotices()->create([
            'user_id' => auth()->user()->id,
            'content' => $request->details,
        ]);

        $toroodResponse = ComplaintHelper::create($complaint);

        if ($complaint)
            return back()->with('success', trans('complaint_added_successfully'));
        else
            return back()->with('error', trans('Somthing_wrong'));

    }


    public function show(Complaint $complaint)
    {
        $this->authorize('isEmployeeHasPermission', 'complaints');
        Carbon::setLocale('ar');
        return Inertia::render('Complaints/Show', [
            'complaint' => [
                'id' => $complaint->id,
                'shipment' => $complaint->shipment->number,
                'complainant' => $complaint->complainant_name,
                'subject' => $complaint->subject,
                'status' => $complaint->status,
                'created_at' => $complaint->created_at->translatedFormat('l j F Y '),
                'end_date' => Carbon::parse($complaint->end_date)->translatedFormat('l j F Y '),
                'case' => $complaint->case == "solved" ? trans('solved') : trans('not_resolved')
            ],
            'shipment' => [
                'status' => $complaint->shipment->status
            ],
            'notices' => $complaint->complaintNotices->map(fn ($notice) => [
                'id' => $notice->id,
                'content' => $notice->content,
                'day' => Carbon::parse($notice->created_at)->translatedFormat('l j F Y '),
                'time' => $notice->created_at->format('H:i'),
                'user' => $notice->user->name,
                'user_id' => $notice->user_id ,
                'user_account' => $notice->user->type,
                'editable' => Auth::user()->can('update', $notice)
            ])->sortByDesc('id')->values()->all()
        ]);
    }



    public function edit(Complaint $complaint)
    {
        $this->authorize('isEmployeeHasPermission', 'complaints');
        return Inertia::render('Complaints/Edit', [
            'complaint' => [
                'id' => $complaint->id,
                'shipment' => $complaint->shipment->number,
                'complainant' => $complaint->complainant_name,
                'subject' => $complaint->subject,
                'status' => $complaint->status,
            ]
        ]);
    }


    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'shipment' => 'required|exists:App\Models\Shipment,number',
        ],
        [
            'shipment.exists' => trans('shipment_number_does_not_exist')
        ]);

        $shipment = Shipment::where('number', $request->shipment)->firstOrFail();

        $complaint->update([
            'shipment_id' => $shipment->id,
            'complainant_name' => $request->complainant,
            'subject' => $request->subject,
        ]);

        return back()->with('success', trans('complaint_updated_successfully'));
    }


    public function updateStatus(Request $request, Complaint $complaint)
    {

        // \DB::beginTransaction();


        if ($complaint->status == 1){

            $request->validate([
                'case' => 'required',
            ],
            [
                'case.required' => trans('required_field')
            ]);

            $complaint->update([
                'status' => 0,
                'case' => $request->case,
                'end_date' => now()
            ]);

        } else
            $complaint->update([
                'status' => 1,
            ]);

        $toroodResponse = ComplaintHelper::tracking($complaint);

        // if (json_decode($toroodResponse)->isSuccess){
        //     \DB::commit();
        //     return back()->with('success', 'تم تعديل الشكوى');
        // }

        // else{
        //     return back()->with('error', 'هناك خطأ مان، أعد المحاولة');
        //     \DB::rollback();
        // }

        // return     $toroodResponse ;

        return back()->with('success', trans('complaint_updated_successfully'));



    }
}
