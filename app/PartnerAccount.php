<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerAccount extends Model
{
    protected $table = 'partner_account';
    protected $primaryKey = 'partner_account_id';
    protected $fillable = ['username', 'password', 'admin_code'];
    protected $hidden = ['password', 'admin_code'];
    public $timestamps = false;

    //new
    public function info()
    {
        return $this->hasOne(\App\PartnerInfo::class, 'partner_account_id', 'partner_account_id');
    }

    public function branches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'partner_account_id', 'partner_account_id');
    }

    public function activeBranches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'partner_account_id', 'partner_account_id')
            ->where('active', 1);
    }

    public function deactiveBranches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'partner_account_id', 'partner_account_id')
            ->where('active', 0);
    }

    public function tnc()
    {
        return $this->hasOne(\App\TncForPartner::class, 'partner_account_id', 'partner_account_id');
    }

    public function profileImage()
    {
        return $this->hasOne(\App\PartnerProfileImage::class, 'partner_account_id', 'partner_account_id');
    }

    public function discount()
    {
        return $this->hasMany(\App\Discount::class, 'partner_account_id');
    }

    public function followPartner()
    {
        return $this->hasMany(\App\FollowPartner::class, 'following', 'partner_account_id');
    }

    public function hotspot()
    {
        return $this->hasMany(\App\Hotspots::class, 'partner_account_id');
    }

    public function notification()
    {
        return $this->hasMany(\App\PartnerNotification::class, 'partner_account_id', 'partner_account_id');
    }

    public function categoryRelation()
    {
        return $this->hasMany(\App\PartnerCategoryRelation::class, 'partner_id', 'partner_account_id');
    }

    public function menuImages()
    {
        return $this->hasMany(\App\PartnerMenuImages::class, 'partner_account_id', 'partner_account_id');
    }

    public function galleryImages()
    {
        return $this->hasMany(\App\PartnerGalleryImages::class, 'partner_account_id', 'partner_account_id');
    }

    public function trending()
    {
        return $this->belongsTo(\App\TrendingOffers::class, 'partner_account_id', 'partner_account_id');
    }

    public function featuredDeals()
    {
        return $this->belongsTo(\App\FeaturedDeals::class, 'partner_account_id', 'partner_account_id');
    }

    public function topBrands()
    {
        return $this->belongsTo(\App\TopBrands::class, 'partner_account_id', 'partner_account_id');
    }

    public function specialDeals()
    {
        return $this->belongsTo(\App\SpecialDeals::class, 'partner_account_id', 'partner_account_id');
    }

    public function stats()
    {
        return $this->hasMany(\App\RbdStatistics::class, 'partner_id', 'partner_account_id');
    }

    public function partnerInHotSpot()
    {
        return $this->hasMany(\App\PartnersInHotspot::class, 'partner_account_id', 'partner_account_id');
    }

    public function rating()
    {
        return $this->hasOne(\App\Rating::class, 'partner_account_id', 'partner_account_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Review::class, 'partner_account_id', 'partner_account_id');
    }

    public function delete()
    {
        // delete all related photos
        $this->info()->delete();
        $this->tnc()->delete();
        $this->discount()->delete();
        $this->followPartner()->delete();
        $this->categoryRelation()->delete();
        $this->profileImage()->delete();
        $this->menuImages()->delete();
        $this->galleryImages()->delete();
        $this->trending()->delete();
        $this->featuredDeals()->delete();
        $this->topBrands()->delete();
        $this->specialDeals()->delete();
        $this->rating()->delete();
        $this->reviews()->delete();
//        $this->stats()->delete();
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // Photo::where("user_id", $this->id)->delete()

        // delete the partner
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
