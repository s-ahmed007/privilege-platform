<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedCard extends Model
{
    protected $table = 'assigned_card';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['card_number', 'status', 'card_type', 'seller_account_id', 'month', 'assigned_on', 'sold_on'];

    public function seller()
    {
        return $this->hasOne(\App\CardSellerAccount::class, 'id', 'seller_account_id');
    }

    public function cardPromoUsage()
    {
        return $this->hasMany(\App\CardPromoCodeUsage::class, 'customer_id', 'card_number');
    }

    public function ssl()
    {
        return $this->hasMany(\App\SslTransactionTable::class, 'customer_id', 'card_number');
    }
}
