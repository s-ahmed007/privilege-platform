<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'rating';
    protected $fillable = ['partner_account_id', '1_star', '2_star', '3_star', '4_star',
        '5_star', 'average_rating', ];
    public $timestamps = false;

    //new
    public function partner()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id');
    }

    public function delete()
    {
        return parent::delete();
    }
}
