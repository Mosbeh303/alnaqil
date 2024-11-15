<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebhookRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Shipment;
use App\Models\SallaOauthToken;
use App\Integrations\Torood\ParcelHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Auth;
use App\Notifications\SendAuthenticationDetails;
use App\Jobs\SallaProcessWebhookJob;
use Illuminate\Http\Request;
use Throwable;


class SallaWebhookController extends Controller
{
    public function __invoke(WebhookRequest $request)
    {

        try {

            $data =  (object) $request;

            // Log::info($data);
            SallaProcessWebhookJob::dispatch($data->data, $data->event , $data->merchant);

            return response('🎉');

        } catch (\Exception $e) {
            Log::error($e);
        }
    }


}
