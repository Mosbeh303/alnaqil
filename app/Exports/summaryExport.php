<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class summaryExport implements FromView
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
        return view('exports.summary_shipments', [
            'shipments' => $this->shipments
        ]);
    }
}
