<?php

namespace App;

use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTable extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_table';
    protected $fillable = [
        'branch_id', 'customer_id', 'amount_spent', 'discount_amount', 'req_id', 'transaction_point',
        'posted_on', 'branch_user_id', 'offer_id', 'review_id', 'transaction_request_id', 'GUID', 'redeem_id', 'platform',
    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($data) {
            if ($data->deleteReviewWithTran) {
                $data->deleteReviewWithTran->delete();
            }
            if ($data->customerNotification) {
                $data->customerNotification->delete();
            }
            if ($data->transactionRequest) {
                $data->transactionRequest->delete();
            }
        });
    }

    //new
    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id'); //foreign key, primary key
    }

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function customerHistory()
    {
        return $this->belongsTo(\App\CustomerHistory::class, 'customer_id', 'customer_id')->latest(); //foreign key, primary key
    }

    public function bonus()
    {
        return $this->hasOne(\App\BonusRequest::class, 'req_id', 'req_id'); //foreign key, primary key
    }

    public function offer()
    {
        return $this->hasOne(\App\BranchOffers::class, 'id', 'offer_id'); //foreign key, primary key
    }

    public function b2b2cUser()
    {
        return $this->belongsTo(\App\B2b2cUser::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function transactionRequest()
    {
        return $this->hasOne(\App\CustomerTransactionRequest::class, 'id', 'transaction_request_id'); //foreign key, primary key
    }

    public function review()
    {
        return $this->hasOne(\App\Review::class, 'id', 'review_id')->where('moderation_status', 1); //foreign key, primary key
    }

    public function deleteReviewWithTran()
    {
        return $this->hasOne(\App\Review::class, 'id', 'review_id'); //foreign key, primary key
    }

    public function customerReviews()
    {
        return $this->hasMany(\App\Review::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function branchUser()
    {
        return $this->hasOne(\App\BranchUser::class, 'id', 'branch_user_id'); //foreign key, primary key
    }

    public function rewardRedeem()
    {
        return $this->belongsTo(\App\CustomerRewardRedeem::class, 'redeem_id', 'id');
    }

    public function customerNotification()
    {
        return $this->hasOne(\App\CustomerNotification::class, 'source_id', 'id')
            ->where('notification_type', \App\Http\Controllers\Enum\notificationType::transaction);
    }

    public function scopeManualTransaction($query)
    {
        return $query->where('branch_user_id', AdminScannerType::manual_transaction);
    }

    public function scopeTotalPoint($query, $customer_id)
    {
        return $query->where('customer_id', $customer_id)->sum('transaction_point');
    }

    public function scopeBranchPoint($query, $customer_id, $branch_id)
    {
        $reward_redeemed = CustomerRewardRedeem::where('customer_id', $customer_id)
            ->with(['reward' => function ($sql) use ($branch_id) {
                $sql->where('branch_id', $branch_id);
            }])->get();
        $total_point_redeemed = 0;
        foreach ($reward_redeemed as $reward) {
            if ($reward->reward) {
                $total_point_redeemed += $reward->reward->selling_point * $reward->quantity;
            }
        }
        $total_branch_point = $query->where('customer_id', $customer_id)->where('branch_id', $branch_id)->sum('transaction_point');

        return $total_branch_point - $total_point_redeemed;
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
