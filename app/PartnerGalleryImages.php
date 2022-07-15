<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerGalleryImages extends Model
{
    protected $table = 'partner_gallery_images';
    protected $fillable = ['partner_account_id', 'partner_gallery_image', 'image_caption'];
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
