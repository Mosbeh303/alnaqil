<?php

namespace App\Jobs;

use App\Models\Complaint;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class CreateComplaint implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $complaint;



    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            "WaybillNumber" => $this->complaint->shipment->number,
            "complaintNumber" => strval($this->complaint->id),
            "complaintStatusDate" => strtotime($this->complaint->updated_at),
            "complaintStatus" => 1
        ];

        try {

            $response = Http::withHeaders([
                'X-Api-Key' => env('TOROOD_API_KEY'),
            ])->withBody(
                json_encode($data), 'application/json'
            )->post(env('TOROOD_SRV_URL') . 'complaint/add');

           // Log::info($response);

        } catch (\Exception $e) {
           // Log::info($e->getMessage());
        }


    }
}
