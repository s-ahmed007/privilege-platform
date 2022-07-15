<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialDeals extends Model
{
    protected $table = 'special_deals';
    protected $fillable = ['partner_account_id', 'image'];
    public $timestamps = false;

    //new
    public function partner()
    {
        return $this->hasMany(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id');
    }

    public function delete()
    {
        return parent::delete();
    }
}
