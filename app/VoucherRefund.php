<?php

namespace App;

use App\Http\Controllers\Enum\AdminNotificationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRefund extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($refund) {
            if ($refund->newRefReqAdminNotification) {
                $refund->newRefReqAdminNotification->delete();
            }
            if ($refund->refReqAcceptAdminNotification) {
                $refund->refReqAcceptAdminNotification->delete();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function purchaseDetails()
    {
        return $this->belongsTo(\App\VoucherPurchaseDetails::class, 'purchase_id', 'id');
    }

    public function newRefReqAdminNotification()
    {
        return $this->hasOne(\App\AdminActivityNotification::class, 'source', 'id')->where('type', AdminNotificationType::new_voucher_refund_request);
    }

    public function refReqAcceptAdminNotification()
    {
        return $this->hasOne(\App\AdminActivityNotification::class, 'source', 'id')->where('type', AdminNotificationType::voucher_refund_request_accept);
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
