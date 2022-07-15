<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfluencerRequest extends Model
{
    protected $table = 'influencer_request';
    protected $fillable = ['full_name', 'blog_name', 'blog_category', 'email', 'facebook_link', 'website_link', 'youtube_link', 'instagram_link'];
    public $timestamps = false;
}
