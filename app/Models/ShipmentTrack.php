<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'user_id',
        'operator_id',
        'status_before_action',
        'status_after_action',
        'action',
        'notice',
        'changes',
        'original'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

}
