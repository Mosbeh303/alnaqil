<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Inertia\Inertia;


class WaybillPrintController extends Controller
{
    public function index(Shipment $shipment){
        return Inertia::render('Shipments/Prints', [
            'prints' => $shipment->waybillPrints()->paginate(10)
            ->withQueryString()
            ->through(fn($print) =>[
                'user' => $print->user,
                'date' => $print->created_at->translatedFormat('l j F Y, H:i:s')
            ]),
            'shipment' => [
                'number' => $shipment->number
            ]

        ]);
    }
}
