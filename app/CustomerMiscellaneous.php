<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerMiscellaneous extends Model
{
    protected $table = 'customer_miscellaneous';
    protected $fillable = ['id', 'customer_id', 'miscellaneous_id', 'deactive_date'];
    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
