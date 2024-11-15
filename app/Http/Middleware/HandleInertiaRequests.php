<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'previous' => fn () => url()->previous(),
            'auth' => [
                'user' => $request->user(),
                'account' => $request->user() ? $request->user()->type : 'guest',
                'permissions' =>  $request->user() && $request->user()->type == 'employee'  && $request->user()->groupe ? array_merge(json_decode($request->user()->groupe->permissions ), json_decode($request->user()->employee->permissions ) ?? [])  : [],
                'operatorType' => $request->user() && $request->user()->operator ? $request->user()->operator->type : null ,
                'storeFeature' => $request->user() && $request->user()->store ? [
                    'refrigerated_transport' => $request->user()->store->refrigerated_transport_active,
                    'exchange' => $request->user()->store->exchange_active,
                    'cod' => $request->user()->store->cod_active,
                    'return' => $request->user()->store->return_active,
                    'invoice' => intval($request->user()->store->invoice_active),
                    'order_id' => $request->user()->store->order_id_active,
                ]: null,
            ],

            'showingNavOnDesktop' => true,
            'csrf' => csrf_token(),
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'shipmentStatus' => [
                '10' => trans('the_shipment_has_been_created'),
                '20' => trans('Shipment_received'),
                '35' => trans('processing_in_progress'),
                '65' => trans('Prepared_awaiting_delivery'),
                '100' => trans('delivered'),
                '-100' => trans('audit'),
                '-1' => trans('canceled')
            ],

        ]);
    }
}
