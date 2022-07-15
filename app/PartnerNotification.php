<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerNotification extends Model
{
    protected $table = 'partner_notification';
    protected $fillable = ['partner_account_id', 'image_link', 'notification_text', 'notification_type', 'source_id',
                        'seen', 'posted_on', ];
    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(\App\PartnerAccount::class, 'partner_account_id', 'partner_account_id'); //foreign key
    }

    public function notificationType()
    {
        return $this->belongsTo(\App\NotificationType::class, 'notification_type', 'id'); //foreign key
    }
}
