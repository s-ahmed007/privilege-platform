<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewComment extends Model
{
    protected $table = 'review_comment';
    protected $fillable = ['review_id', 'comment', 'comment_type', 'moderation_status'];
    public $timestamps = false;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($data) {
            $data->deleteCustomerNotification($data->review->id);
        });
    }

    //new
    public function review()
    {
        return $this->belongsTo(\App\Review::class, 'review_id', 'id');
    }

    public function deleteCustomerNotification($review_id)
    {
        $notification = CustomerNotification::where('source_id', $review_id)->where('notification_type',
            \App\Http\Controllers\Enum\notificationType::reply_review)->first();
        if ($notification) {
            $notification->delete();
        }
    }

    public function scopePending($query)
    {
        return $query->where('moderation_status', 0);
    }

    public function delete()
    {
        return parent::delete();
    }
}
