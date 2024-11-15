<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class WebhookController extends Controller
{
    public function url(Request $request){
        $webhook = auth()->user()->webhook ;

        if ($webhook){
            $webhook->update([
                'url' => $request->url,
            ]);
        }else {
            auth()->user()->webhook->create([
                'url' => $request->url,
                'secret' => \Illuminate\Support\Str::random()
            ]);
        }

        return back();
    }

    public function generateToken(){
        $webhook = auth()->user()->webhook ;
        $webhook->update([
            'secret' => \Illuminate\Support\Str::random()
        ]);
        return Redirect::back()->with('success', trans('new_notification_key_generated_successfully'));
    }

    public function updateStatus(){
        $webhook = auth()->user()->webhook ;
        if ($webhook->active == 1){
            $webhook->update([
                'active' => 0
            ]);
            return back()->with('success', trans('notification_disabled'));
        }else {
            $webhook->update([
                'active' => 1
            ]);
            return back()->with('success', trans('notification_enabled'));
        }
    }
}
