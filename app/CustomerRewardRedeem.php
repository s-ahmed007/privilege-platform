<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRewardRedeem extends Model
{
    protected $fillable = ['offer_id', 'customer_id', 'used', 'quantity', 'required_fields'];

    use SoftDeletes;

    protected $casts = [
        'required_fields' => 'json',

    ];

    public function reward()
    {
        return $this->hasOne(\App\BranchOffers::class, 'id', 'offer_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }
}
