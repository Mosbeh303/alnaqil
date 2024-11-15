<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class CityExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $cities;

    public function __construct($cities)
    {
        $this->cities = $cities;
    }

    public function view(): View
    {
        return view('exports.cities', [
            'cities' => $this->cities
        ]);
    }
}
