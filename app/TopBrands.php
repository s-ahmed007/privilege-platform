<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopBrands extends Model
{
    protected $table = 'top_brands';
    protected $fillable = ['partner_account_id'];
    public $timestamps = false;

    //new
    public function partner()
    {
        return $this->hasMany(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id');
    }

    public function info()
    {
        return $this->hasOne(\App\PartnerInfo::class, 'partner_account_id', 'partner_account_id');
    }

    public function delete()
    {
        return parent::delete();
    }
}
