<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchVoucher extends Model
{
    use SoftDeletes;

    protected $casts = [
        'date_duration' => 'json',
        'weekdays' => 'json',
        'time_duration' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($voucher) {
            foreach ($voucher->purchaseDetails as $purchase) {
                $purchase->delete();
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(\App\VoucherPurchaseDetails::class, 'voucher_id', 'id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
