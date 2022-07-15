<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerBranch extends Model
{
    protected $table = 'partner_branch';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'password', 'partner_account_id', 'partner_email', 'partner_mobile', 'partner_address', 'partner_location', 'longitude', 'latitude', 'zip_code', 'partner_area', 'partner_division', 'main_branch', 'active', 'owner_id', 'facilities', 'created_at', 'updated_at'];
    protected $hidden = ['password'];
    protected $casts = [
        'facilities' => 'json',
    ];

    public function tnc()
    {
        return $this->hasOne(\App\TncForPartner::class, 'partner_account_id', 'partner_account_id');
    }

    public function couponPayment()
    {
        return $this->hasOne(\App\RbdCouponPayment::class, 'branch_id', 'id');
    }

    public function openingHours()
    {
        return $this->hasOne(\App\OpeningHours::class, 'branch_id', 'id');
    }

    //branch belongs to
    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); //foreign key, primary key
    }

    public function info()
    {
        return $this->belongsTo(\App\PartnerInfo::class, 'partner_account_id', 'partner_account_id'); //foreign key, primary key
    }

    public function transaction()
    {
        return $this->hasMany(\App\TransactionTable::class, 'branch_id', 'id');
    }

    public function coupons()
    {
        return $this->hasMany(\App\AllCoupons::class, 'branch_id', 'id');
    }

    public function partnerInHotspot()
    {
        return $this->belongsTo(\App\PartnersInHotspot::class, 'id', 'branch_id'); //foreign key, primary key
    }

    public function users()
    {
        return $this->hasMany(\App\BranchScanner::class, 'branch_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(\App\BranchOffers::class, 'branch_id', 'id')->where('selling_point', null);
    }

    public function rewards()
    {
        return $this->hasMany(\App\BranchOffers::class, 'branch_id', 'id')
            ->where('selling_point', '!=', null);
    }

    public function owner()
    {
        return $this->belongsTo(\App\BranchOwner::class, 'owner_id', 'id'); //foreign key, primary key
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1)->get(); //foreign key, primary key
    }

    public function branchScanner()
    {
        return $this->hasMany(\App\BranchScanner::class, 'branch_id', 'id'); //foreign key, primary key
    }

    public function vouchers()
    {
        return $this->hasMany(\App\BranchVoucher::class, 'branch_id', 'id');
    }

    public function delete()
    {
        $this->coupons()->delete();
        $this->openingHours()->delete();
        $this->partnerInHotspot()->delete();
        $this->offers()->delete();

        // delete the user
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
