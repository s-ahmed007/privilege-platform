<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerProfileImage extends Model
{
    protected $table = 'partner_profile_images';
    protected $fillable = ['partner_account_id', 'partner_profile_image', 'partner_thumb_image', 'partner_cover_photo'];
    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); //foreign key
    }

    public function delete()
    {
        return parent::delete();
    }
}
