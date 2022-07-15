<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2b2cInfo extends Model
{
    protected $table = 'b2b2c_info';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'phone', 'email', 'image'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(\App\B2b2cUser::class, 'b2b2c_id', 'id');
    }
}
