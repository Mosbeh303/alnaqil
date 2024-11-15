<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Financial;
use DB;

class FinancialController extends Controller
{

    public function index(Request $request){

        $this->authorize('isEmployeeHasPermission', 'financials');

        if($request->type == 'monthly'){
            $quarters = Financial::WhereHas('shipment', function ($query) {
                $query->whereIn('status', [100,-100]);
            })
            ->select( DB::raw('sum(tax) as `tax`, sum(base_fee) as `base_fee`, sum(exchange_fee) as `exchange_fee` , sum(refrigerated_transport_fee) as `refrigerated_transport_fee`, sum(extra_fees) as `extra_fees`, sum(cod_fee) as `cod_fee` ,count(id) as `count`' ) , DB::raw( DB::raw( 'YEAR(updated_at) year, MONTH(updated_at) month' ) ))
            ->orderBy('year', 'asc')
            ->groupBy('year','month')
            ->get()
            ->map(fn ($financial) => [
                    'tax' => number_format((float)(calculateFees($financial) - (calculateFees($financial) / 1.15)), 2, '.', '')  , // $financial->tax
                    'profit' => calculateFees($financial),
                    'count' => $financial->count,
                    'year' => $financial->year,
                    'month' => $financial->month,

            ]);
        }elseif ($request->type == 'annualy'){
            $quarters = Financial::WhereHas('shipment', function ($query) {
                $query->whereIn('status', [100,-100]);
            })
            ->select( DB::raw('sum(tax) as `tax`, sum(base_fee) as `base_fee`, sum(exchange_fee) as `exchange_fee` , sum(refrigerated_transport_fee) as `refrigerated_transport_fee`, sum(extra_fees) as `extra_fees`, sum(cod_fee) as `cod_fee` ,count(id) as `count`' ) , DB::raw( DB::raw( 'YEAR(updated_at) year' ) ))
            ->orderBy('year', 'asc')
            ->groupBy('year')
            ->get()
            ->map(fn ($financial) => [
                    'tax' => number_format((float)(calculateFees($financial) - (calculateFees($financial) / 1.15)), 2, '.', '')  , // $financial->tax
                    'profit' => calculateFees($financial),
                    'count' => $financial->count,
                    'year' => $financial->year,
                    'quarter' => $financial->quarter,

            ]);
        }else{
            $quarters = Financial::WhereHas('shipment', function ($query) {
                $query->whereIn('status', [100,-100]);
            })
            ->select( DB::raw('sum(tax) as `tax`, sum(base_fee) as `base_fee`, sum(exchange_fee) as `exchange_fee` , sum(refrigerated_transport_fee) as `refrigerated_transport_fee`, sum(extra_fees) as `extra_fees`, sum(cod_fee) as `cod_fee` ,count(id) as `count`' ) , DB::raw( DB::raw( 'YEAR(updated_at) year, QUARTER(updated_at) quarter' ) ))
            ->orderBy('year', 'asc')
            ->groupBy('year','quarter')
            ->get()
            ->map(fn ($financial) => [
                    'tax' => number_format((float)(calculateFees($financial) - (calculateFees($financial) / 1.15)), 2, '.', '')  , // $financial->tax
                    'profit' => calculateFees($financial),
                    'count' => $financial->count,
                    'year' => $financial->year,
                    'quarter' => $financial->quarter,

            ]);
        }


        return Inertia::render('Financials/Index', [
            'years' => $quarters->sortByDesc('year')->groupBy('year')->values()->all(),
            'period'  => $request->type
        ]);
    }
}
