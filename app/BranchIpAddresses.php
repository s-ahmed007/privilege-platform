<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchIpAddresses extends Model
{
    protected $table = 'branch_ip_addresses';
    protected $primaryKey = 'id';
    protected $fillable = ['ip_address', 'branch_id'];
    public $timestamps = false;
}
