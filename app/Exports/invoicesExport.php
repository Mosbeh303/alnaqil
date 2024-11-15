<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class invoicesExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $items;




    public function __construct($items)
    {
        $this->items = $items;


    }

    public function view(): View
    {
        return view('exports.invoices', [
            'items' => $this->items,
        ]);
    }
}
