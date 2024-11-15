<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;
use App\Models\Receiver;


class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $shipment;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(1);

        try {
            $message = getSettingValue('receiving_shipment_message');

            if ($message) {
                $message = str_replace('{phone}', $this->shipment->receiver_phone, $message);
                $message = str_replace('{number}', $this->shipment->number, $message);
                $message = str_replace('{receiver}', $this->shipment->receiver_name, $message);
                $message = str_replace('{store}', $this->shipment->store->name, $message);
                $message = str_replace('{amount}', $this->shipment->financial->cod, $message);
                $message = str_replace('{content}', $this->shipment->details, $message);



                $instanceId = "132709";
                $token = "59cddf89-2b8e-4f68-9792-47ae17815747";
                $phone = substr_replace($this->shipment->receiver_phone,"966",0,1);;
                $body = urlencode($message);

                $url = "https://api.4whats.net/sendMessage/?instanceid={$instanceId}&token={$token}&phone={$phone}&body={$body}";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ));

                $response = curl_exec($curl);
                $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = curl_error($curl);

                curl_close($curl);

                if ($error) {
                    echo "cURL Error #: " . $error;
                } else {
                    $response_json = json_decode($response, true);

                    if ($status_code == 200) {
                        if (isset($response_json["messages"][0]["err_text"])) {
                            echo $response_json["messages"][0]["err_text"];
                        } else {
                            //  echo "تم الارسال بنجاح  " . " job id:" . $response_json["job_id"];
                        }
                    } elseif ($status_code == 400) {
                        echo $response_json["message"];
                    } elseif ($status_code == 422) {
                        echo "نص الرسالة فارغ";
                    } else {
                        echo "محظور بواسطة كلاودفلير. Status code: {$status_code}";
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info($e);
        }
    }
}
