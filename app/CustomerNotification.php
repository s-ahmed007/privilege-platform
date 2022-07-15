<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerNotification extends Model
{
    protected $table = 'customer_notification';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'image_link',
        'notification_text', 'notification_type', 'source_id', 'posted_on', ];
    public $timestamps = false;

    public function info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'user_id', 'customer_id'); // this matches the Eloquent model
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
