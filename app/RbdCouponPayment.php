<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RbdCouponPayment extends Model
{
    protected $table = 'rbd_coupon_payment';
    protected $fillable = ['id', 'branch_id', 'total_amount', 'paid_amount', 'updated_at'];
    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id'); //foreign key, primary key
    }
}
