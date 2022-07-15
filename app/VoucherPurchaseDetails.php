<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherPurchaseDetails extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($purchaseDetails) {
            if ($purchaseDetails->ssl) {
                $purchaseDetails->ssl->delete();
            }
            if ($purchaseDetails->refund) {
                $purchaseDetails->refund->delete();
            }
        });
    }

    public function voucher()
    {
        return $this->belongsTo(\App\BranchVoucher::class, 'voucher_id', 'id');
    }

    public function ssl()
    {
        return $this->belongsTo(\App\VoucherSslInfo::class, 'ssl_id', 'id');
    }

    public function voucherHistory()
    {
        return $this->belongsTo(\App\VoucherHistory::class, 'ssl_id', 'ssl_id');
    }

    public function refund()
    {
        return $this->hasOne(\App\VoucherRefund::class, 'purchase_id', 'id');
    }

    public function review()
    {
        return $this->hasOne(\App\Review::class, 'id', 'review_id')->where('moderation_status', 1);
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
