<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
    protected $table = 'openings';
    protected $fillable = ['position', 'duration', 'salary', 'deadline', 'requirements'];
}
