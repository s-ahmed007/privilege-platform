<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SharePost extends Model
{
    protected $table = 'share_post';
    protected $fillable = ['post_id', 'sharer_id', 'sharer_type', 'sharer_type'];
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo(\App\Post::class, 'id', 'post_id'); //foreign key
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
