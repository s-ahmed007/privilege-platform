<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassChanged extends Model
{
    protected $table = 'pass_changed';
    protected $fillable = ['customer_id', 'pass_change'];
    public $timestamps = false;

    //new
    public function account()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id'); //foreign key, primary key
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
