<?php

namespace App;

use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use Illuminate\Database\Eloquent\Model;

class CustomerTransactionRequest extends Model
{
    protected $table = 'customer_transaction_request';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($data) {
            foreach ($data->partnerNotifications as $item) {
                $item->delete();
            }
        });
    }

    public function offer()
    {
        return $this->belongsTo(\App\BranchOffers::class, 'offer_id', 'id');
    }

    public function customerInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function transaction()
    {
        return $this->hasOne(\App\TransactionTable::class, 'transaction_request_id', 'id');
    }

    public function redeem()
    {
        return $this->hasOne(\App\CustomerRewardRedeem::class, 'id', 'redeem_id');
    }

    public function partnerNotifications()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'source_id', 'id')->where('notification_type',
            PartnerBranchNotificationType::TRANSACTION_REQUEST); //foreign key
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }

    public function branchScanner()
    {
        return $this->hasOne(\App\BranchScanner::class, 'branch_user_id', 'updated_by');
    }
}
