<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Exception;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user;
    protected $title;
    protected $body;

    public function __construct($user, $title, $body)
    {
        $this->onConnection('sync');
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $registrationIds = $this->user->devicesTokens->pluck('value')->toArray();

            if (empty($registrationIds)) {
                throw new Exception('No device tokens found for the user.');
            }

            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $serverKey = env('FCM_SERVER_KEY');
            $headers = [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $responses = [];

            foreach ( $this->user->devicesTokens as $deviceToken) {
                $titleText = ($deviceToken->language === 'ar') ?  $this->title['ar'] :   $this->title['en'];
                $bodyText = ($deviceToken->language === 'ar') ?   $this->body['ar'] :   $this->body['en'];

                $notification = [
                    'title' => $titleText,
                    'body' => $bodyText,
                    'sound' => 'default',
                ];

                $message = [
                    'registration_ids' => [$deviceToken->value],
                    'notification' => $notification,
                ];

                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($ch);
                $responses[] = $response;
            }

            curl_close($ch);

            Log::info("FCM Responses: " . json_encode($responses));

            return $responses;
        } catch (Exception $e) {
            Log::error("FCM Notification Error: " . $e->getMessage());
            return false;
        }
    }
}