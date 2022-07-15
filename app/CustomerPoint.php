<?php

namespace App;

use App\Http\Controllers\Enum\PointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPoint extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'point', 'point_type', 'source_id'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($data) {
            if ($data->customerNotification) {
                $data->customerNotification->delete();
            }
        });
    }

    public function scopeRoyaltyPoint($query, $customer_id)
    {
        return $query->where('customer_id', $customer_id)->sum('point');
    }

    public function scopeReferPoint($query, $customer_id)
    {
        return $query->where('customer_id', $customer_id)->where(function ($fun_query) {
            $fun_query->where('point_type', PointType::refer_point)
                ->orWhere('point_type', PointType::referred_by_point);
        })->sum('point');
    }

    public function scopeRatingPoint($query, $customer_id)
    {
        return  $query->where([['customer_id', $customer_id], ['point_type', PointType::rating_point]])->sum('point');
    }

    public function scopeReviewPoint($query, $customer_id)
    {
        return  $query->where([['customer_id', $customer_id], ['point_type', PointType::review_point]])->sum('point');
    }

    public function scopeProfileCompletePoint($query, $customer_id)
    {
        return  $query->where([['customer_id', $customer_id], ['point_type', PointType::profile_completion_point]])->sum('point');
    }

    public function dealRefund()
    {
        return  $this->belongsTo(\App\VoucherRefund::class, 'source_id', 'id');
    }

    public function dealPurchaseDetails()
    {
        return  $this->belongsTo(\App\VoucherPurchaseDetails::class, 'source_id', 'id');
    }

    public function scopeDealRedeemedPoint($query, $customer_id)
    {
        return  $query->where([['customer_id', $customer_id], ['point_type', PointType::deal_redeem_point]])->sum('point');
    }

    public function scopeDealRefundPoint($query, $customer_id)
    {
        return  $query->where([['customer_id', $customer_id], ['point_type', PointType::deal_refund_point]])->sum('point');
    }

    public function review()
    {
        return $this->hasOne(\App\Review::class, 'id', 'source_id');
    }

    public function sourceCustomerInfo()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'source_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function customerNotification()
    {
        return $this->hasOne(\App\CustomerNotification::class, 'source_id', 'id')
            ->where('notification_type', \App\Http\Controllers\Enum\notificationType::reward);
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
