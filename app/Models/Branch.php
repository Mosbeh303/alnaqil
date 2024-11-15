<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
