<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupsDepartment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function groups()
    {
        return $this->hasMany(Groupe::class);
    }
}
