<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResetUser extends Model
{
    protected $table = 'reset_user';
    protected $fillable = ['id', 'customer_id', 'token', 'used', 'verification_type', 'email'];

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
