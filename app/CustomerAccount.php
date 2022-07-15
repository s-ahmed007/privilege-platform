<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Model
{
    protected $table = 'customer_account';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'customer_id', 'customer_serial_id', 'customer_username', 'password',
        'moderator_status', 'isSuspended', 'pin', 'platform',
    ];
    protected $hidden = ['password'];
    public $timestamps = false;
    public $incrementing = false;

    public function info()
    {
        return $this->hasOne(\App\CustomerInfo::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    //new
    public function passChanged()
    {
        return $this->hasOne(\App\PassChanged::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function wish()
    {
        return $this->hasMany(\App\Wish::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function followPartner()
    {
        return $this->hasMany(\App\FollowPartner::class, 'follower', 'customer_id'); // this matches the Eloquent model
    }

    public function followerCustomer()
    {
        return $this->hasMany(\App\FollowCustomer::class, 'follower', 'customer_id'); // this matches the Eloquent model
    }

    public function followingCustomer()
    {
        return $this->hasMany(\App\FollowCustomer::class, 'following', 'customer_id'); // this matches the Eloquent model
    }

    public function reviewLikes()
    {
        return $this->hasMany(\App\LikesReview::class, 'liker_id', 'customer_id'); // this matches the Eloquent model
    }

    public function postLikes()
    {
        return $this->hasMany(\App\LikePost::class, 'liker_id', 'customer_id'); // this matches the Eloquent model
    }

    public function transaction()
    {
        return $this->hasMany(\App\TransactionTable::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function SslTransaction()
    {
        return $this->hasMany(\App\SslTransactionTable::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function social()
    {
        return $this->hasOne(\App\SocialId::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function stats()
    {
        return $this->hasMany(\App\RbdStatistics::class, 'customer_id', 'customer_id');
    }

    public function miscellaneous()
    {
        return $this->hasOne(\App\CustomerMiscellaneous::class, 'customer_id', 'customer_id');
    }

    public function reset_user()
    {
        return $this->hasMany(\App\ResetUser::class, 'customer_id', 'customer_id');
    }

    public function delete()
    {
        $this->info->delete();
        $this->social()->delete();
        $this->followingCustomer()->delete();
        $this->followerCustomer()->delete();
        $this->followPartner()->delete();
        $this->postLikes()->delete();
        $this->reviewLikes()->delete();
        $this->passChanged()->delete();
        $this->stats()->delete();
        $this->transaction()->delete();
        $this->miscellaneous()->delete();
        $this->reset_user()->delete();

        // delete the customer
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
