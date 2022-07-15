<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerCreditRedeemed extends Model
{
    protected $table = 'seller_credit_redeemed';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['credit', 'seller_account_id', 'status', 'posted_on', 'debit'];

    public function account()
    {
        return $this->belongsTo(\App\CardSellerAccount::class, 'seller_account_id', 'id');
    }
}
