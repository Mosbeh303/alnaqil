<?php

namespace App\Integrations\Torood;
use App\Models\Complaint ;
use Illuminate\Support\Facades\Http;

use App\Jobs\CreateComplaint;
use App\Jobs\ComplaintTracking;

class ComplaintHelper{

    public static function create(Complaint $complaint){
        CreateComplaint::dispatch($complaint);
    }

    public static function tracking($complaint){
        ComplaintTracking::dispatch($complaint);
    }
}
