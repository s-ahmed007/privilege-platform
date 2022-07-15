<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannerReward extends Model
{
    protected $table = 'scanner_reward';
    protected $primaryKey = 'id';
    protected $fillable = ['scanner_id', 'point', 'point_used'];
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
