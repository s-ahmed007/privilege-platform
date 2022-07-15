<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningHours extends Model
{
    protected $table = 'opening_hours';
    protected $fillable = ['branch_id', 'sat', 'sun', 'mon', 'tue', 'wed', 'thurs', 'fri'];
    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(\App\PartnerBranch::class, 'branch_id', 'id'); //foreign key
    }

    public function delete()
    {
        // delete the table
        try {
            return parent::delete();
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
