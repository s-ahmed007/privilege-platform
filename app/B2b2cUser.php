<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2b2cUser extends Model
{
    protected $table = 'b2b2c_user';
    protected $primaryKey = 'id';
    protected $fillable = ['b2b2c_id', 'customer_id'];
    public $timestamps = false;

    public function customerInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function transaction()
    {
        return $this->hasMany(\App\TransactionTable::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function b2b2cInfo()
    {
        return $this->belongsTo(\App\B2b2cInfo::class, 'b2b2c_id', 'id');
    }
}
