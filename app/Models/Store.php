<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marketer()
    {
        return $this->belongsTo(Marketer::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }


    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }


    public function zid()
    {
        return $this->hasOne(Zid::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('neighborhood', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                    });
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
                $query->when($status == 'approved' , function($query){
                    $query->where('status', 'approved');
                }, function($query){
                    $query->where('status','!=', 'approved');
                });
        });

    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function token()
    {
        return $this->hasOne(SallaOauthToken::class);
    }

    public function pickupAddresses(){
        return $this->hasMany(PickupAddress::class);
    }

    public function defaultPickupAddress(){
        return $this->pickupAddresses()->where('default', 1)->first();
    }


}