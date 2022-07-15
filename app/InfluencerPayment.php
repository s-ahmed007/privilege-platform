<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfluencerPayment extends Model
{
    protected $table = 'rbd_influencer_payment';
    protected $primaryKey = 'id';
    protected $fillable = ['influencer_id', 'total_amount', 'paid_amount', 'updated_at'];
    public $timestamps = false;

    public function promoInfo()
    {
        return $this->belongsTo(\App\CardPromoCodes::class, 'influencer_id', 'influencer_id');
    }

    public function userInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'influencer_id', 'customer_id');
    }
}
