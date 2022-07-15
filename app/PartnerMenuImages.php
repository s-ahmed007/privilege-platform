<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerMenuImages extends Model
{
    protected $table = 'partner_menu_images';
    protected $fillable = ['partner_account_id', 'partner_menu_image'];
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
