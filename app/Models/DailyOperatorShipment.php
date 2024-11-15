<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOperatorShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'operator_id' ,
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
