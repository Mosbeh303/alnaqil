<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class storeSummaryExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $items;

    public $sum;

    public function __construct($items, $sum)
    {
        $this->items = $items;

        $this->sum = $sum;
    }

    public function view(): View
    {
        return view('exports.store_summary', [
            'items' => $this->items,

            'sum' => $this->sum,

        ]);
    }
}
