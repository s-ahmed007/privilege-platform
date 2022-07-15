<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchScanner extends Model
{
    protected $table = 'branch_scanner';
    protected $primaryKey = 'id';
    protected $fillable = ['first_name', 'last_name', 'ip_authorized',
        'designation', 'branch_id', 'branch_user_id', 'full_name', ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($branchScanner) {
            if ($branchScanner->scannerReward) {
                $branchScanner->scannerReward->delete();
            }
            foreach ($branchScanner->scannerPrizeHistory as $row) {
                $row->delete();
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id');
    }

    public function branchUser()
    {
        return $this->belongsTo(\App\BranchUser::class, 'branch_user_id', 'id');
    }

    public function scannerReward()
    {
        return $this->hasOne(\App\ScannerReward::class, 'scanner_id', 'id');
    }

    public function scannerPrizeHistory()
    {
        return $this->hasMany(\App\ScannerPrizeHistory::class, 'scanner_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(\App\TransactionTable::class, 'branch_user_id', 'branch_user_id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
