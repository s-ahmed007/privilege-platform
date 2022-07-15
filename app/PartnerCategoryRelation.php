<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerCategoryRelation extends Model
{
    protected $table = 'part_cat_rel';
    protected $primaryKey = 'id';
    protected $fillable = ['cat_rel_id', 'partner_id'];
    public $timestamps = false;

    //part_cat_rel relation belongs to
    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_id', 'partner_account_id'); //foreign key of partner_account table
    }

    public function info()
    {
        return $this->belongsTo(\App\PartnerInfo::class, 'partner_id', 'partner_account_id'); //foreign key of partner_account table
    }

    public function branches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'partner_account_id', 'partner_id'); //foreign key of partner_account table
    }

    //part_cat_rel belongs to
    public function categoryRelation()
    {
        return $this->belongsTo(\App\CategoryRelation::class, 'cat_rel_id', 'id'); //foreign key of cat_rel table
    }

    public static function boot()
    {
        parent::boot();
    }
}
