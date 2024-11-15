<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class EmployeeExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $vouchers;

    public function __construct($vouchers)
    {
        $this->vouchers = $vouchers;
    }

    public function view(): View
    {
        return view('exports.employee', [
            'vouchers' => $this->vouchers
        ]);
    }
}
