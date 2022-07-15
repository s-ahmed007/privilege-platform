<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardPromoType extends Model
{
    protected $table = 'card_promo_type';
    protected $primaryKey = 'id';
    protected $fillable = ['type'];
    public $timestamps = false;

    public function promoCodes()
    {
        return $this->hasMany(\App\CardPromoCodes::class, 'type', 'id');
    }
}
