<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherPayment extends Model
{
    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id');
    }

    public function paidHistory()
    {
        return $this->hasMany(\App\BranchCreditRedeemed::class, 'branch_id', 'branch_id')->orderBy('id', 'DESC');
    }
}
