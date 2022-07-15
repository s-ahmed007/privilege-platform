<?php

namespace App;

use App\Http\Controllers\Enum\AdminNotificationType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
    protected $table = 'review';
    protected $fillable = ['partner_account_id', 'customer_id', 'heading', 'rating', 'body', 'platform', 'moderation_status'];
    public $timestamps = false;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($review) {
            if ($review->adminNotifications) {
                $review->adminNotifications->delete();
            }

            if ($review->ratingPointNotification) {
                $review->ratingPointNotification->delete();
            }

            if ($review->reviewPointNotification) {
                $review->reviewPointNotification->delete();
            }

            foreach ($review->comments as $comment) {
                $comment->delete();
            }
            foreach ($review->likes as $like) {
                $like->delete();
            }
            foreach ($review->partnerNotifications as $notification) {
                $notification->delete();
            }
            $review->makeTransactionReviewableAgain($review->id);
        });
        static::deleted(function ($review) {
            $review->reCalculateRatings($review->partner_account_id);
        });
    }

    public function comments()
    {
        return $this->hasMany(\App\ReviewComment::class, 'review_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function customer_info()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function likes()
    {
        return $this->hasMany(\App\LikesReview::class, 'review_id', 'id');
    }

    public function partnerInfo()
    {
        return $this->belongsTo(\App\PartnerInfo::class, 'partner_account_id', 'partner_account_id');
    }

    public function customerInfo()
    {
        return $this->belongsTo(\App\CustomerInfo::class, 'customer_id', 'customer_id');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\TransactionTable::class, 'id', 'review_id');
    }

    public function dealPurchase()
    {
        return $this->belongsTo(\App\VoucherPurchaseDetails::class, 'id', 'review_id');
    }

    public function scopeRating($query, $partner_account_id)
    {
        $all = $query->where('partner_account_id', $partner_account_id)->get();
        if (count($all) > 0) {
            return $all->sum('rating') / $all->count();
        } else {
            return 0;
        }
    }

    public function notificationReviews()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'source_id', 'id')
            ->where('notification_type', PartnerBranchNotificationType::REVIEW_POST);
    }

    public function ratingPointNotification()
    {
        return $this->hasOne(\App\CustomerPoint::class, 'source_id', 'id')
            ->where('point_type', PointType::rating_point);
    }

    public function reviewPointNotification()
    {
        return $this->hasOne(\App\CustomerPoint::class, 'source_id', 'id')
            ->where('point_type', PointType::review_point);
    }

    public function scopeRatingChanges($query, $partner_account_id, $days)
    {
        $all = $query->where('partner_account_id', $partner_account_id)->get();

        $prev = $query->where('partner_account_id', $partner_account_id)->where('posted_on', '<=', Carbon::now()->subDays($days))->get();

        if ($prev->count() > 0) {
            $prev = $prev->sum('rating') / $prev->count();
            $prev = round($prev, 2);
        } else {
            $prev = 0;
        }

        if ($all->count() > 0) {
            $all = $all->sum('rating') / $all->count();
            $all = round($all, 2);
        } else {
            $all = 0;
        }
        if ($all > $prev) {
            return 'Increased from '.$prev.' to '.$all.' in last '.$days.' days';
        } elseif ($all < $prev) {
            return 'Decreased to '.$all.' from '.$prev.' in last '.$days.' days';
        } else {
            return 'No changes'.' in last '.$days.' days';
        }
    }

    public function makeTransactionReviewableAgain($review_id)
    {
        $transaction = TransactionTable::where('review_id', $review_id)->first();
        if ($transaction) {
            $transaction->review_id = null;
            $transaction->save();
        } else {
            $dealPurchase = VoucherPurchaseDetails::where('review_id', $review_id)->first();
            if ($dealPurchase) {
                $dealPurchase->review_id = null;
                $dealPurchase->save();
            }
        }
    }

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }

    public function partnerNotifications()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'source_id', 'id')->where('notification_type',
            PartnerBranchNotificationType::REVIEW_POST); //foreign key
    }

    public function adminNotifications()
    {
        return $this->hasOne(\App\AdminActivityNotification::class, 'source', 'id')->where('type',
            AdminNotificationType::user_review); //foreign key
    }

    public function reCalculateRatings($partner_id)
    {
        $totalReview = self::where('partner_account_id', $partner_id)->count();
        // average rating
        if ($totalReview != 0) {
            $average = self::where('partner_account_id', $partner_id)->sum('rating');
            $average = $average / $totalReview;
            $average = round($average, 2);
            for ($i = 1; $i <= 5; $i++) {
                //total specific star partner got
                $totalStars = self::where('rating', $i)
                    ->where('partner_account_id', $partner_id)
                    ->count();
                //percentage of the specific star
                $starPercentage = $totalStars / $totalReview * 100;
                DB::table('rating')
                    ->where('partner_account_id', $partner_id)
                    ->update([
                        $i.'_star' => $starPercentage,
                        'average_rating' => $average,
                    ]);
            }
        } else {
            $average = 0;
            for ($i = 1; $i <= 5; $i++) {
                DB::table('rating')
                    ->where('partner_account_id', $partner_id)
                    ->update([
                        $i.'_star' => '0.00',
                        'average_rating' => $average,
                    ]);
            }
        }
    }
}
