<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'balance',

    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }


}
