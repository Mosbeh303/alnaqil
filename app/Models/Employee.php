<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'dues',
        'branch_id',
        'city_id',
        'neighborhood',
        'id_number',
        'permissions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function reimbursements()
    {
        return $this->hasMany(EmpReimbursement::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }


}
