<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Press extends Model
{
    protected $table = 'press';
    protected $fillable = ['press_name', 'sub_title', 'press_details', 'press_image', 'press_link', 'date'];
    public $timestamps = false;
}
