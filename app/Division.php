<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'division';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public function areas()
    {
        return $this->hasMany(\App\Area::class, 'division_id', 'id'); // this matches the Eloquent model
    }
}
