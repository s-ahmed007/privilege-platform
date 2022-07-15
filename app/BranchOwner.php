<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchOwner extends Model
{
    protected $table = 'branch_owner';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'password', 'name', 'phone', 'active'];
    protected $hidden = ['password'];
    public $timestamps = false;

    public function branches()
    {
        return $this->hasMany(\App\PartnerBranch::class, 'owner_id', 'id');
    }

    public function delete()
    {
        // delete the partner
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
