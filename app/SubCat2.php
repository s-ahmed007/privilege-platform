<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCat2 extends Model
{
    protected $table = 'sub_cat_2';
    protected $fillable = ['cat_name'];
    public $timestamps = false;

    //new
    public function categoryRelation()
    {
        return $this->belongsToMany(\App\CategoryRelation::class, 'category_relation', 'sub_cat_1_id'); //foreign key of CategoryRelation table
    }
}
