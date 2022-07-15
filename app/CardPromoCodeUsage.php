<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardPromoCodeUsage extends Model
{
    protected $table = 'customer_card_promo_usage';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'promo_id', 'ssl_id'];
    public $timestamps = false;

    //new
    public function customerInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function promoCode()
    {
        return $this->belongsTo(\App\CardPromoCodes::class, 'promo_id', 'id');
    }

    public function ssl()
    {
        return $this->belongsTo(\App\SslTransactionTable::class, 'ssl_id', 'id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
