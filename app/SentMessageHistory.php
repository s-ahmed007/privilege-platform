<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentMessageHistory extends Model
{
    protected $table = 'sent_message_history';
    use SoftDeletes;

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '!=', null)->where('sent', 0)->orderBy('id', 'DESC');
    }
}
