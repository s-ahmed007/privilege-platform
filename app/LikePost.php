<?php

namespace App;

use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use Illuminate\Database\Eloquent\Model;

class LikePost extends Model
{
    protected $table = 'likes_post';
    protected $fillable = ['post_id', 'liker_id', 'liker_type'];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($data) {
            foreach ($data->partnerNotifications as $notification) {
                $notification->delete();
            }
        });
    }

    //discount belongs to
    public function post()
    {
        return $this->belongsTo(\App\Post::class, 'post_id', 'id'); //foreign key, primary key
    }

    public function customer()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'liker_id'); //foreign key, primary key
    }

    public function partner()
    {
        return $this->hasOne(\App\PartnerInfo::class, 'partner_account_id', 'liker_id'); //foreign key, primary key
    }

    public function partnerNotifications()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'source_id', 'id')->where('notification_type',
            PartnerBranchNotificationType::LIKE_POST); //foreign key
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
