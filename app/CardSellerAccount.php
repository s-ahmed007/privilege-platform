<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CardSellerAccount extends Authenticatable implements JWTSubject
{
    protected $table = 'card_seller_account';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['username', 'password', 'phone', 'role', 'active', 'f_token'];
    protected $hidden = ['password'];

    public function info()
    {
        return $this->hasOne(\App\CardSellerInfo::class, 'seller_account_id', 'id'); // this matches the Eloquent model
    }

    public function balance()
    {
        return $this->hasOne(\App\SellerBalance::class, 'seller_id', 'id'); // this matches the Eloquent model
    }

    public function cardSold()
    {
        return $this->hasMany(\App\CustomerHistory::class, 'seller_id', 'id'); // this matches the Eloquent model
    }

    public function creditRedeemed()
    {
        return $this->hasMany(\App\SellerCreditRedeemed::class, 'seller_account_id', 'id'); // this matches the Eloquent model
    }

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
}
