<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoTable extends Model
{
    protected $table = 'promo_table';
    protected $fillable = ['partner_name', 'image_link', 'category', 'discount_percentage', 'partner_website',
        'promo_code', 'term&condition', ];
    public $timestamps = false;
}
