<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'shipment_id',
        'complainant_name',
        'status',
        'subject',
        'case',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function complaintNotices()
    {
        return $this->hasMany(ComplaintNotice::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%'.$search.'%')
                    ->orWhere('complainant_name', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%'.$search.'%');
                    })->orWhereHas('shipment', function ($query) use ($search) {
                        $query->where('number', 'like', '%'.$search.'%')
                        ->orWhereHas('store', function ($query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
                        });
                    });
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
                $query->when($status == 2 , function($query){
                    $query->where('status', 0);
                }, function($query){
                    $query->where('status', 1);
                });
        });

    }
}
