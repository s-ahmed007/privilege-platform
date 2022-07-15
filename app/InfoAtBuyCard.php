<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfoAtBuyCard extends Model
{
    protected $table = 'info_at_buy_card';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'tran_id', 'customer_serial_id',
        'customer_username', 'password', 'moderator_status', 'customer_first_name', 'customer_last_name', 'customer_full_name',
        'customer_email', 'customer_dob', 'customer_gender', 'customer_contact_number', 'customer_address', 'customer_profile_image',
        'customer_type', 'month', 'expiry_date', 'member_since', 'referral_number', 'reference_used',
        'card_active', 'card_activation_code', 'firebase_token', 'delivery_status', 'review_deleted', 'delivery_type',
        'shipping_address', 'customer_social_id', 'customer_social_type', 'card_promo_id', 'order_date', 'paid_amount', 'platform', ];
    public $timestamps = false;

    public function info()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }
}
