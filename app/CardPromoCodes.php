<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardPromoCodes extends Model
{
    protected $table = 'card_promo';
    protected $primaryKey = 'id';
    protected $fillable = ['code', 'active', 'text', 'type', 'expiry_date', 'usage', 'influencer_id'];
    public $timestamps = false;

    //new
    public function promoType()
    {
        return $this->hasOne(\App\CardPromoType::class, 'id', 'type');
    }

    public function promoUsage()
    {
        return $this->hasMany(\App\CardPromoCodeUsage::class, 'promo_id', 'id'); //foreign key of CategoryRelation table
    }

    public function userInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'influencer_id', 'customer_id');
    }

    public function influencerPayment()
    {
        return $this->hasOne(\App\InfluencerPayment::class, 'influencer_id', 'influencer_id');
    }

    public function scopeActive($query)
    {
        return $query->where([['active', 1], ['expiry_date', '>=', date('Y-m-d')]]);
    }
}
