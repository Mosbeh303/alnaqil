<?php

namespace App\Exports;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class JntExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $shipments;

    public function __construct($shipments)
    {
        $this->shipments = $shipments;
    }

    public function view(): View
    {
        return view('exports.shipments', [
            'shipments' => $this->shipments
        ]);
    }
}
