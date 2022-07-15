<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchUserNotification extends Model
{
    protected $table = 'branch_user_notification';
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    public function branchUser()
    {
        return $this->belongsTo(\App\BranchScanner::class, 'branch_user_id', 'branch_user_id');
    }

    public function customerInfo()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function transactionRequest()
    {
        return $this->hasOne(\App\CustomerTransactionRequest::class, 'id', 'source_id');
    }

    public function scopeUnseenCount($query, $user_id)
    {
        return $query->where('branch_user_id', $user_id)->where('seen', 0)->count();
    }

    public function likedPost()
    {
        return $this->belongsTo(\App\LikePost::class, 'source_id', 'id');
    }

    public function review()
    {
        return $this->belongsTo(\App\Review::class, 'source_id', 'id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
