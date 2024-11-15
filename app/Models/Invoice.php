<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function financials()
    {
        return $this->hasMany(Financial::class);
    }


    public function fees(){
        $fees1 = $this->financials()->where('ADP', null)->sum(DB::raw('IFNULL(base_fee, 0) + IFNULL(exchange_fee, 0) + IFNULL(refrigerated_transport_fee, 0) + IFNULL(extra_fees, 0) + IFNULL(cod_fee, 0) + IFNULL(addtional_fees_extra_weight, 0)'));
        $fees2 = $this->financials()->where('ADP', '!=' ,null)->sum('ADP');
        return $fees1 + $fees2 ;

    }

}