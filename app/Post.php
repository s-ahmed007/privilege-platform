<?php

namespace App;

use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'post';
    protected $fillable = ['poster_id', 'image_url', 'poster_type', 'caption', 'moderate_status', 'push_status',
        'post_link', 'header', 'pinned_post', ];
    public $timestamps = false;

    public function like()
    {
        return $this->hasMany(\App\LikePost::class, 'post_id', 'id'); //foreign key of likes_post table
    }

    public function partnerBranch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'poster_id', 'id'); //foreign key
    }

    public function sharePost()
    {
        return $this->hasMany(\App\SharePost::class, 'post_id', 'id'); //foreign key
    }

    public function notificationPosts()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'source_id', 'id')->where('notification_type', PartnerBranchNotificationType::LIKE_POST);
    }

    public function delete()
    {
        $this->like()->delete();
        $this->sharePost()->delete();
        $this->notificationPosts()->delete();
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
