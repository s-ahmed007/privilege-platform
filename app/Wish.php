<?php

namespace App;

use App\Http\Controllers\Enum\PartnerRequestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wish extends Model
{
    protected $table = 'wish';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'comment', 'posted_on', 'partner_request_type'];
    public $timestamps = false;

    use SoftDeletes;

    public static function boot()
    {
        parent::boot();
    }

    public function account()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id');
    }

    public function branchUser()
    {
        return $this->belongsTo(\App\BranchUser::class, 'customer_id', 'id');
    }

    public function scopeWish($query)
    {
        return $query->where('partner_request_type', null)->orderBy('id', 'DESC');
    }

    public function scopeOfferRequest($query)
    {
        return $query->where('partner_request_type', PartnerRequestType::offer_request)->orderBy('id', 'DESC');
    }

    public function scopeRewardRequest($query)
    {
        return $query->where('partner_request_type', PartnerRequestType::reward_request)->orderBy('id', 'DESC');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
