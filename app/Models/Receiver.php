<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{

    protected $fillable = [
        'name',
        'phone',
        'city',
        'neighborhood',
        'street',
        'id_number',
        'door_number',
        'location_data',
        'location_adress',
        'door_photo',
        'national_adress',
        'link'

    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('neighborhood', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('id_number', 'like', '%'.$search.'%');
            });
        });
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
    use HasFactory;
}
