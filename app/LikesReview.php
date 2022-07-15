<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LikesReview extends Model
{
    protected $table = 'likes_review';
    protected $fillable = ['review_id', 'liker_id', 'liker_type'];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($like) {
            if ($like->customerNotifications) {
                $like->customerNotifications->delete();
            }
        });
    }

    //discount belongs to
    public function review()
    {
        return $this->belongsTo(\App\Review::class, 'review_id', 'id'); //foreign key, primary key
    }

    public function customer()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'liker_id'); //foreign key, primary key
    }

    public function branch()
    {
        return $this->hasOne(\App\PartnerBranch::class, 'id', 'liker_id'); //foreign key, primary key
    }

    public function customerNotifications()
    {
        return $this->hasOne(\App\CustomerNotification::class, 'source_id', 'id')->where('notification_type',
            \App\Http\Controllers\Enum\notificationType::like_review); //foreign key
    }

//    public function liker()
//    {
//        return $this->hasManyThrough(
//            'App\CustomerInfo',
//            'App\PartnerInfo',
//            'partner_account_id',
//            'customer_id',
//            'liker_id',
//            'partner_account_id'
//        );
//    }

    public function delete()
    {
        return parent::delete();
    }
}
