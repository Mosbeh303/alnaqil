<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;


class WebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Log::info($this->header());
        // Log::info("authorization1: " . $this->header('authorization')[0]);
        // Log::info("authorization2: " . $this->header('authorization'));
        // Log::info("authorization3: " . env('WEBHOOK_CLIENT_SECRET'));
        return $this->header('authorization') === env('WEBHOOK_CLIENT_SECRET');
    }

    public function rules()
    {
        return [
            'event'    => ['required'],
            'merchant' => ['required'],
            'data'     => ['required'],
        ];
    }
}
