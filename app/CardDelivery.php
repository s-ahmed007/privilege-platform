<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardDelivery extends Model
{
    protected $table = 'card_delivery';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'delivery_type', 'shipping_address'];
    public $timestamps = false;

    public function info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function sslTransaction()
    {
        return $this->belongsTo(\App\SslTransactionTable::class, 'ssl_id', 'id'); // this matches the Eloquent model
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
