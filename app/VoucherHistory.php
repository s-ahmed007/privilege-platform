<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherHistory extends Model
{
    protected $table = 'voucher_history';

    use SoftDeletes;

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id');
    }

    public function voucherDetails()
    {
        return $this->hasMany(\App\VoucherPurchaseDetails::class, 'ssl_id', 'ssl_id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
