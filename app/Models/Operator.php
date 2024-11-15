<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'branch_id',
        'city_id',
        'neighborhood',
        'allowed',
        'unpaid_dues',
        'identification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('unpaid_dues', 'like', '%'.$search.'%')
                    ->orWhere('identification', 'like', '%'.$search.'%')
                    ->orWhere('neighborhood', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('lastname', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                    })->orWhereHas('branch', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    })->orWhereHas('city', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        })->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type','=', $type);
        });

    }

    public function dailyShipments()
    {
        return $this->hasMany(DailyOperatorShipment::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }


}
