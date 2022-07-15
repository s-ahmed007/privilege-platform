<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_type';
    protected $fillable = ['type'];
    public $timestamps = false;

    public function info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_type', 'id');
    }

    public function discount()
    {
        return $this->belongsTo(\App\Discount::class, 'user_type', 'id');
    }
}
