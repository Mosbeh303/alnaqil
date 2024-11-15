<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zid extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zid_store_id',
        'store_id',
        'access_token',
        'refresh_token',
        'token_type',
        'expires_in',
        'authorization',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }


}
