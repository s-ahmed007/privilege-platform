<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SslTransactionTable extends Model
{
    protected $table = 'ssl_transaction_table';
    public $timestamps = false;
    protected $guarded = [];

    //new
    public function customer()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function socialId()
    {
        return $this->belongsTo(\App\SocialId::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function cardDelivery()
    {
        return $this->hasOne(\App\CardDelivery::class, 'ssl_id', 'id'); // this matches the Eloquent model
    }

    public function promoUsage()
    {
        return $this->hasOne(\App\CardPromoCodeUsage::class, 'ssl_id', 'id'); // this matches the Eloquent model
    }

    public function sellerCommission()
    {
        return $this->hasOne(\App\SellerCommissionHistory::class, 'ssl_id', 'id');
    }

    public function delete()
    {
        $this->customer()->delete();
        $this->info()->delete();
        $this->reward()->delete();
        $this->socialId()->delete();
        // delete the user
        return parent::delete();
    }
}
