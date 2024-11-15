<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'title',
        'file',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
