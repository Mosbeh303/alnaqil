<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupAddress extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function shipments(){
        return $this->hasMany(Shipment::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
