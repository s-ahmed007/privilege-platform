<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchFacility extends Model
{
    use SoftDeletes;
    protected $casts = [
        'category_ids' => 'json',
    ];
}
