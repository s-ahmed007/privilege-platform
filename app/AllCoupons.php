<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllCoupons extends Model
{
    protected $table = 'all_coupons';
    protected $primaryKey = 'id';
    protected $fillable = ['branch_id', 'coupon_type', 'reward_text', 'coupon_details',
        'coupon_tnc', 'stock', 'posted_on', 'expiry_date', ];
    public $timestamps = false;

    public function bonusRequest()
    {
        return $this->hasMany(\App\BonusRequest::class, 'coupon_id', 'id'); // this matches the Eloquent model
    }

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id'); // this matches the Eloquent model
    }

    public function delete()
    {
        $this->bonusRequest()->delete();
        // delete the table
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
