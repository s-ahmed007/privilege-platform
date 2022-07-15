<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $table = 'blog_post';
    protected $fillable = ['id', 'image_url', 'details', 'heading', 'category_id', 'active_status', 'posted_on', 'priority'];
    public $timestamps = false;

    public function BlogCategory()
    {
        return $this->belongsTo(\App\BlogCategory::class, 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }
}
