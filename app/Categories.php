<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['type', 'name'];
    // public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($category) {
            foreach ($category->catRel as $relation) {
                $relation->delete();
            }
        });
    }

    //new
    public function info()
    {
        return $this->hasMany(\App\PartnerInfo::class, 'partner_category', 'id'); //foreign key
    }

    public function featuredPartners()
    {
        return $this->hasMany(\App\FeaturedDeals::class, 'category_id', 'id')
            ->orderBy('order_num', 'ASC')->with('partner'); //foreign key
    }

    public function catRel()
    {
        return $this->hasMany(\App\CategoryRelation::class, 'main_cat', 'id'); //foreign key of CategoryRelation table
    }
}
