<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerInfo extends Model
{
    protected $table = 'partner_info';
    protected $primaryKey = 'id';
    protected $fillable = ['partner_account_id', 'partner_name', 'owner_name', 'owner_contact', 'partner_category', 'partner_type', 'facebook_link', 'website_link', 'instagram_link', 'about', 'expiry_date', 'created_at', 'updated_at'];

    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); // foreign key of info table, primary key of account table (because I made 'partner_account_id' as primary key instead of 'id')
    }

    public function branches()
    {
        return $this->hasManyThrough(
            \App\PartnerBranch::class,
            \App\PartnerAccount::class,
            'partner_account_id',
            'partner_account_id',
            'partner_account_id',
            'partner_account_id'
        );
    }

    public function activeBranches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'partner_account_id', 'partner_account_id')
            ->where('active', 1);
    }

    public function category()
    {
        return $this->belongsTo(\App\Categories::class, 'partner_category', 'id');
    }

    public function profileImage()
    {
        return $this->hasOne(\App\PartnerProfileImage::class, 'partner_account_id', 'partner_account_id');
    }

    public function featuredDeals()
    {
        return $this->belongsTo(\App\FeaturedDeals::class, 'partner_account_id', 'partner_account_id');
    }

    public function rating()
    {
        return $this->hasOne(\App\Rating::class, 'partner_account_id', 'partner_account_id');
    }

    public function galleryImages()
    {
        return $this->hasMany(\App\PartnerGalleryImages::class, 'partner_account_id', 'partner_account_id');
    }

    public function menuImages()
    {
        return $this->hasMany(\App\PartnerMenuImages::class, 'partner_account_id', 'partner_account_id');
    }

    public function discount()
    {
        return $this->hasMany(\App\Discount::class, 'partner_account_id', 'partner_account_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Review::class, 'partner_account_id', 'partner_account_id');
    }

    public function tnc()
    {
        return $this->hasOne(\App\TncForPartner::class, 'partner_account_id', 'partner_account_id');
    }

    public function PartnerCategoryRelation()
    {
        return $this->hasMany(\App\PartnerCategoryRelation::class, 'partner_id', 'partner_account_id');
    }

    public function delete()
    {
        // delete the user
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
