<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffers extends Model
{
    protected $table = 'branch_offers';
    protected $primaryKey = 'id';
    protected $fillable = ['branch_id', 'date_duration', 'weekdays', 'time_duration', 'point', 'active', 'offer_description',
        'price', 'counter_limit', 'scan_limit', 'point_customize_id', 'actual_price', 'tnc', 'valid_for', 'offer_full_description',
        'priority', 'image', 'selling_point', 'created_at', 'updated_at', ];

    use SoftDeletes;

    protected $casts = [
        'date_duration' => 'json',
        'weekdays' => 'json',
        'time_duration' => 'json',
        'required_fields' => 'json',

    ];

    public function customizedPoint()
    {
        return $this->hasOne(\App\CustomizePoint::class, 'id', 'point_customize_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id'); //foreign key, primary key
    }

    public function branchScanner()
    {
        return $this->hasMany(\App\BranchScanner::class, 'branch_id', 'branch_id'); //foreign key, primary key
    }

    public function rewardRedeems()
    {
        return $this->hasMany(\App\CustomerRewardRedeem::class, 'offer_id', 'id'); //foreign key, primary key
    }

    public function rewardAvailed()
    {
        return $this->hasMany(\App\CustomerRewardRedeem::class, 'offer_id', 'id')->where('used', 1); //foreign key, primary key
    }

    public function scopeOffers($query, $branch_id)
    {
        return $query->where('branch_id', $branch_id)->where('selling_point', null);
    }

    public function scopeRewards($query, $branch_id)
    {
        return $query->where('branch_id', $branch_id)->where('selling_point', '!=', null);
    }

    public function scopeActiveOffers($query)
    {
        return $query->where('active', 1)->first();
    }
}
