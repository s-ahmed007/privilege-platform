<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerFacilities extends Model
{
    protected $table = 'partner_facilities';
    protected $primaryKey = 'id';
    protected $fillable = ['branch_id', 'card_payment', 'kids_area', 'outdoor_seating', 'smoking_area',
                        'reservation', 'wifi', 'concierge', 'online_booking', 'seating_area', ];
    public $timestamps = false;

    //facilities belong to
    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id');
    }

    public function delete()
    {
        // delete the table
        try {
            return parent::delete();
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
