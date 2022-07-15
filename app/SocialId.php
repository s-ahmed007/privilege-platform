<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialId extends Model
{
    protected $table = 'social_id';
    protected $fillable = ['customer_id', 'customer_social_id', 'customer_social_type'];
    public $timestamps = false;

    //new
    public function account()
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
