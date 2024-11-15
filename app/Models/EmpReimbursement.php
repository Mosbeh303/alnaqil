<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'number',
        'amount',
        'notice',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
