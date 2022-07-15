<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllAmounts extends Model
{
    protected $table = 'all_amounts';
    protected $primaryKey = 'id';
    protected $fillable = ['type', 'price', 'month'];
    public $timestamps = false;
}
