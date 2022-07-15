<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowCustomer extends Model
{
    protected $table = 'follow_customer';
    protected $primaryKey = 'id';
    protected $fillable = ['follower', 'following', 'posted_on'];
    public $timestamps = false;

    public function follower()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'follower', 'customer_id'); // this matches the Eloquent model
    }

    public function following()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'following', 'customer_id'); // this matches the Eloquent model
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
