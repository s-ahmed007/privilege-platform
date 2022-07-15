<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeaturedDeals extends Model
{
    protected $table = 'featured_deals';
    protected $primaryKey = 'id';
    protected $fillable = ['partner_account_id', 'category_id', 'order_num'];
    public $timestamps = false;

    public function partner()
    {
        return $this->hasOne(\App\PartnerInfo::class, 'partner_account_id', 'partner_account_id');
    }

    public function delete()
    {
        return parent::delete();
    }
}
