<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerHistory extends Model
{
    protected $table = 'customer_history';

    use SoftDeletes;

    public function customerInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function sellerInfo()
    {
        return $this->belongsTo(\App\CardSellerInfo::class, 'seller_id', 'seller_account_id');
    }

    public function sslInfo()
    {
        return $this->belongsTo(\App\SslTransactionTable::class, 'ssl_id', 'id')->where('status', 1);
    }

    public function customerPaymentHistory()
    {
        return $this->hasMany(\App\SslTransactionTable::class, 'customer_id', 'customer_id')->where('status', 1);
    }
}
