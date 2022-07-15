<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusRequest extends Model
{
    protected $table = 'bonus_request';
    protected $primaryKey = 'req_id';
    protected $fillable = ['coupon_id', 'customer_id', 'used', 'request_code', 'expiry_date'];
    public $timestamps = false;

    //new
    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function coupon()
    {
        return $this->belongsTo(\App\AllCoupons::class, 'coupon_id', 'id'); //foreign key, primary key
    }

    public function transaction()
    {
        return $this->belongsTo(\App\TransactionTable::class, 'req_id', 'req_id'); //foreign key, primary key
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
