<?php

namespace App\Exports;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View ;

class CommissionExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $commissions;

    public function __construct($commissions)
    {
        $this->commissions = $commissions;
    }

    public function view(): View
    {
        return view('exports.commissions', [
            'commissions' => $this->commissions
        ]);
    }
}
