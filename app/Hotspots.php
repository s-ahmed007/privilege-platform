<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hotspots extends Model
{
    protected $table = 'hotspots';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image_link', 'description'];
    public $timestamps = false;

    //new
    public function hotspotPartners()
    {
        return $this->hasMany(\App\PartnersInHotspot::class, 'hotspot_id', 'id'); //foreign key of notification table
    }
}
