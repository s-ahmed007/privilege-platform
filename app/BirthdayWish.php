<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BirthdayWish extends Model
{
    protected $table = 'birthday_wish';
    protected $primaryKey = 'id';
    protected $fillable = ['customer_id', 'expiry_date', 'posted_on'];
    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
