<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerCommissionHistory extends Model
{
    protected $table = 'seller_commission_history';

    public function ssl()
    {
        return $this->belongsTo(\App\SslTransactionTable::class, 'ssl_id', 'id');
    }
}
