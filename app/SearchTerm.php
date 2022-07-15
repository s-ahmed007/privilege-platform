<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchTerm extends Model
{
    protected $casts = [
        'images' => 'json',
        'branches' => 'json'
    ];
}
