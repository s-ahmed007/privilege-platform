<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannerPrizeHistory extends Model
{
    protected $table = 'scanner_prize_history';
    protected $primaryKey = 'id';
    protected $fillable = ['text', 'point', 'scanner_id', 'status', 'posted_on', 'request_comment'];
    public $timestamps = false;

    public function branchScanner()
    {
        return $this->belongsTo(\App\BranchScanner::class, 'scanner_id', 'id');
    }

    public function delete()
    {
        // delete the customer
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
