<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'notification_type';
    protected $fillable = ['type'];
    public $timestamps = false;

    public function partnerNotification()
    {
        return $this->hasOne(\App\PartnerNotification::class, 'notification_type', 'id'); //foreign key
    }
}
