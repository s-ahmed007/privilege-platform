<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id';
    protected $fillable = ['area_name', 'division_id'];
    public $timestamps = false;

    public function division()
    {
        return $this->belongsTo(\App\Division::class, 'division_id', 'id'); // this matches the Eloquent model
    }
}
