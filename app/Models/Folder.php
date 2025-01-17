<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function wallets()
    {
        return $this->hasMany(FolderWallet::class);
    }
}
