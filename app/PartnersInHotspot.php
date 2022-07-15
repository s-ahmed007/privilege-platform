<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnersInHotspot extends Model
{
    protected $table = 'partners_in_hotspot';
    protected $fillable = ['hotspot_id', 'branch_id'];
    public $timestamps = false;

    //new
    public function hotspot()
    {
        return $this->belongsTo(\App\Hotspots::class, 'hotspot_id', 'id'); //foreign key
    }

    public function branch()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'id', 'branch_id'); //foreign key
    }

    public function delete()
    {
        return parent::delete();
    }
}
