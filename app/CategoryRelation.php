<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryRelation extends Model
{
    protected $table = 'category_relation';
    protected $primaryKey = 'id';
    protected $fillable = ['main_cat', 'sub_cat_1_id'];
    public $timestamps = false;

    public function mainCategory()
    {
        return $this->belongsTo(\App\Categories::class, 'main_cat', 'id'); //foreign key of cat_rel table, primary key of sub_cat_1 table
    }

    public function sub_cat_1()
    {
        return $this->belongsTo(\App\SubCat1::class, 'sub_cat_1_id', 'id'); //foreign key of cat_rel table, primary key of sub_cat_1 table
    }

    public function sub_cat_2()
    {
        return $this->belongsTo(\App\SubCat2::class, 'sub_cat_2_id', 'id'); //foreign key of cat_rel table, primary key of sub_cat_1 table
    }

    public function partnerCategory()
    {
        return $this->hasMany(\App\PartnerCategoryRelation::class, 'cat_rel_id', 'id'); //foreign key of branch table
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            $item->partnerCategory()->delete();
        });
    }
}
