<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class StoresExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $stores;

    public function __construct($stores)
    {
        $this->stores = $stores ;
    }

    public function view(): View
    {
        return view('exports.stores', [
            'stores' => $this->stores
        ]);
    }
}