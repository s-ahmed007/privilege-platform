<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowPartner extends Model
{
    protected $table = 'follow_partner';
    protected $primaryKey = 'id';
    protected $fillable = ['follower', 'following', 'posted_on'];
    public $timestamps = false;

    public function follower()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'follower', 'customer_id'); // this matches the Eloquent model
    }

    public function following()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'following', 'partner_account_id'); // this matches the Eloquent model
    }

    public function delete()
    {
        return parent::delete();
    }
}
