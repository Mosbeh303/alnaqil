<?php

namespace App\Http\Controllers;
use Log;
use Illuminate\Http\Request;
use App\Http\Requests\WebhookRequest;

class JtexpressWebhookController extends Controller
{
    public function __invoke(WebhookRequest $request)
    {
        // Log::info($request);
        // Log::info('I am here');
        // return $request;
    }
}
