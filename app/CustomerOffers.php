<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOffers extends Model
{
    protected $table = 'customer_offers';
    protected $primaryKey = 'id';
    protected $fillable = ['bonus_id', 'customer_id', 'used'];
    public $timestamps = false;
}
