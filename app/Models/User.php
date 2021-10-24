<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Constants\Roles;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'phone',
        'otp_verified',
        'otp_code',
        'email_verified',
        'lang_pref',
        'newsletter_status',
        'otp_email_verify',
        'otp_email_expiry'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsTo(\App\Models\Roles::class, 'role_id');
    }

    public function representativeDiscountHistories()
    {
        return $this->hasMany(RepresentativeDiscountHistory::class, 'user_id');
    }

    public function isSuperAdmin()
    {
        return $this->role_id == Roles::SUPERADMIN;
    }

    public function isRepresentative()
    {
        return $this->role_id == Roles::REPRESENTATIVE;
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'user_id');
    }

    public function repCoupons()
    {
       return $this->hasMany(RepCoupon::class, 'rep_id');
    }

    public function repPayments()
    {
        return $this->hasMany(RepresentativePayments::class, 'rep_id');
    }
}
