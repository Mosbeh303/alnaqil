<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Log;
use Exception;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'branch_id',
        'lastname',
        'phone',
        'email',
        'password',
        'username',
        'type',
        'groupe_id',
        'avatar',
        'id_number'
    ];


    public static function boot()
    {
        parent::boot();

        static::created(function($user)
        {

            try{

                if ((substr($user->phone, 0, 3) == '966') && $user->type == 'client'){
                    $user->phone = 0 . substr($user->phone, 3);
                }

            } catch (\Exception $e) {

                Log::info($e->getMessage());

            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function marketer(){
        return $this->hasOne(Marketer::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function operator()
    {
        return $this->hasOne(Operator::class);
    }



    public function shipmentNotices()
    {
        return $this->hasMany(ShipmentNotice::class);
    }

    public function shipmentTracks()
    {
        return $this->hasMany(ShipmentTrack::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function custodies() //  custodianship of funds
    {
        return $this->hasMany(Custody::class);
    }

    public function groupe(){
        return $this->belongsTo(Groupe::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function webhook()
    {
        return $this->hasOne(Webhook::class);
    }

    public function zid()
    {
        return $this->hasOne(Zid::class);
    }

    public function devicesTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

}
