<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerJoinForm extends Model
{
    protected $table = 'partner_join_form';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['business_name', 'business_number', 'business_name', 'business_email', 'business_address', 'full_name', 'partner_division', 'business_area', 'business_category', 'fb_link'];
}
