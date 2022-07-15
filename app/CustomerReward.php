<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerReward extends Model
{
    protected $table = 'customer_reward';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'customer_reward', 'coupon', 'refer_bonus', 'bonus_counter'];
    public $timestamps = false;

    public function info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
