<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerBalance extends Model
{
    protected $table = 'seller_balance';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['seller_id', 'credit', 'credit_used', 'debit', 'debit_used'];
}
