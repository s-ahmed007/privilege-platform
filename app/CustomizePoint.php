<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomizePoint extends Model
{
    protected $table = 'point_customize';
    protected $primaryKey = 'id';
    protected $fillable = ['type', 'date_duration', 'weekdays', 'time_duration', 'point'];
    public $timestamps = false;
    protected $casts = [
        'date_duration' => 'array',
        'weekdays' => 'array',
        'time_duration' => 'array',

    ];
}
