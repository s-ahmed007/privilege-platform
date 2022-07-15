<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoyaltyLogEvents extends Model
{
    protected $table = 'royalty_log_events';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['event', 'event_value', 'customer_id', 'posted_on'];
}
