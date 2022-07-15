<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSslInfo extends Model
{
    protected $table = 'voucher_ssl_info';
    public $timestamps = false;
    use SoftDeletes;

    public function voucherHistory()
    {
        return $this->hasOne(\App\VoucherHistory::class, 'ssl_id', 'id');
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
