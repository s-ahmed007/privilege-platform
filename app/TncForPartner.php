<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TncForPartner extends Model
{
    protected $table = 'tnc_for_partner';
    protected $fillable = ['partner_account_id', 'terms&condition', 'posted_on'];
    public $timestamps = false;

    //discount belongs to
    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); //foreign key, primary key
    }

    public function delete()
    {
        return parent::delete();
    }
}
