<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCat1 extends Model
{
    protected $table = 'sub_cat_1';
    protected $fillable = ['cat_name'];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

//        static::deleting(function ($item) {
//            $item->itemCounter()->delete();
//            $item->tags()->delete();
//            $item->images()->delete();
//        });
    }

    public function sub_cat_2()
    {
        return $this->hasMany(\App\CategoryRelation::class, 'sub_cat_1_id', 'id'); //foreign key of CategoryRelation table
    }

    public function category()
    {
        return $this->belongsToMany(\App\CategoryRelation::class, 'category_relation', 'main_cat'); //foreign key of CategoryRelation table
    }
}
