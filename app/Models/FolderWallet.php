<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderWallet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
