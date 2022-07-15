<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_category';
    protected $fillable = ['id', 'category'];
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany(\App\BlogPost::class, 'category_id', 'id');
    }
}
