<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RbdStatistics extends Model
{
    protected $table = 'rbd_statistics';
    protected $fillable = ['customer_id', 'partner_id', 'ip_address', 'browser_data'];
    public $timestamps = false;

    //new
    public function customer()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id');
    }

    public function partner()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_id', 'partner_account_id');
    }

    public function partnerInfo()
    {
        return $this->belongsTo(\App\PartnerInfo::class, 'partner_id', 'partner_account_id');
    }

    public function delete()
    {
        // delete the table
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
