<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchRewardPayment extends Model
{
    protected $table = 'branch_reward_payment';
    use SoftDeletes;

    public function scopeTotalPaid($query, $branch_id)
    {
        return $query->where('branch_id', $branch_id)->sum('amount');
    }
}
