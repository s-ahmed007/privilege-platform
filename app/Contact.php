<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'comment', 'posted_on'];
    public $timestamps = false;

    public function delete()
    {
        // delete the customer
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
