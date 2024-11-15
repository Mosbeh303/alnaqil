<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'owner_name',
        'rib',
    ];

    public function store()
    {
        return $this->belongsTo(User::class);
    }

}
