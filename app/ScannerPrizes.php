<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannerPrizes extends Model
{
    protected $table = 'scanner_prizes';
    protected $primaryKey = 'id';
    protected $fillable = ['text', 'point'];
    public $timestamps = false;
}
