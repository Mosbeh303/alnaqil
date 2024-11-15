<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'for',
        'groups_department_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermessions()
    {
        return $this->hasMany(GroupeHasPermission::class);
    }

    public function depart()
    {
        return $this->belongsTo(GroupsDepartment::class);
    }
}
