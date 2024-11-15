<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
