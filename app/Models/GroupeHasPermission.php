<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupeHasPermission extends Model
{
    use HasFactory;

    public function groupe()
    {
        return $this->belongsTo(Group::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

}
