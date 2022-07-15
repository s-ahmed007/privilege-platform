<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardSellerInfo extends Model
{
    protected $table = 'card_seller_info';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['first_name', 'last_name', 'seller_account_id', 'pin', 'commission', 'trial_commission', 'promo_ids'];

    protected $casts = [
        'promo_ids' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(\App\CardSellerAccount::class, 'seller_account_id', 'id');
    }

    public function salesHistory()
    {
        return $this->hasMany(\App\CustomerHistory::class, 'seller_id', 'seller_account_id');
    }

    public function commissionHistory()
    {
        return $this->hasMany(\App\SellerCommissionHistory::class, 'seller_id', 'id');
    }
}
