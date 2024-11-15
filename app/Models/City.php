<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function District()
    {
        return $this->belongsTo(District::class);
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
