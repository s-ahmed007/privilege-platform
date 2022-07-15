<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discount';
    protected $primaryKey = 'id';
    protected $casts = [
        'extra_discount' => 'array',
    ];
    protected $fillable = ['partner_account_id', 'discount_percentage', 'discount_details', 'user_type', 'expiry_date', 'extra_discount'];
    public $timestamps = false;

    //discount belongs to
    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); //foreign key, primary key
    }

    //new
    public function type()
    {
        return $this->hasOne(\App\UserType::class, 'user_type', 'id');
    }

    public function customizedPoint()
    {
        return $this->hasOne(\App\CustomizePoint::class, 'id', 'point_customize_id');
    }

    public function delete()
    {
        return parent::delete();
    }
}
