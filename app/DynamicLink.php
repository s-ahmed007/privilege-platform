<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicLink extends Model
{
    use SoftDeletes;
    protected $casts = [
        'values' => 'json',
    ];
}
