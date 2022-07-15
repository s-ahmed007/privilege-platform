<?php

namespace App;

use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\CustomerType;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CustomerInfo extends Authenticatable implements JWTSubject
{
    protected $table = 'customer_info';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['customer_id', 'customer_first_name', 'customer_last_name', 'customer_full_name',
        'customer_email', 'customer_dob', 'customer_gender', 'customer_contact_number', 'customer_address',
        'customer_profile_image', 'customer_type', 'month', 'expiry_date', 'member_since', 'referral_number', 'reference_used',
        'card_active', 'card_activation_code', 'firebase_token', 'delivery_status', 'review_deleted', 'referrer_id', ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function account()
    {
        return $this->belongsTo(\App\CustomerAccount::class, 'customer_id', 'customer_id');
    }

    public function customerHistory()
    {
        return $this->hasOne(\App\CustomerHistory::class, 'customer_id', 'customer_id')->latest(); //foreign key, primary key
    }

    public function latestCheckout()
    {
        return $this->hasOne(\App\TransactionTable::class, 'customer_id', 'customer_id')
            ->orderBy('posted_on', 'DESC'); //foreign key, primary key
    }

    public function isUpgrade()
    {
        $history = CustomerHistory::where('customer_id', $this->customer_id)->get();
        if (count($history) > 1) {
            if ($history[count($history) - 1]->type == CustomerType::card_holder) {
                if ($history[count($history) - 2]->type == CustomerType::trial_user) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function customerLastActivitySession()
    {
        return $this->hasOne(\App\CustomerActivitySession::class, 'customer_id', 'customer_id')->latest(); //foreign key, primary key
    }

//    public function customerReferrer()
//    {
//        return $this->hasOne('App\CustomerNotification', 'user_id', 'customer_id')->where('notification_type', 10)
//            ->where('notification_text', '%has joined%'); //foreign key, primary key
//    }

    public function customerReferrer()
    {
        return $this->hasOne(self::class, 'customer_id', 'referrer_id'); //foreign key, primary key
    }

    public function latestSSLTransaction()
    {
        return $this->hasOne(\App\SslTransactionTable::class, 'customer_id', 'customer_id')->where('status', 1)
            ->orderBy('id', 'DESC')
            ->with('cardDelivery'); //foreign key, primary key
    }

    public function assignedCard()
    {
        return $this->belongsTo(\App\AssignedCard::class, 'customer_id', 'card_number');
    }

    public function type()
    {
        return $this->hasOne(\App\UserType::class, 'id', 'customer_type');
    }

    public function cardDelivery()
    {
        return $this->hasOne(\App\CardDelivery::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function cardDeliveries()
    {
        return $this->hasMany(\App\CardDelivery::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function bonusRequest()
    {
        return $this->hasMany(\App\BonusRequest::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function birthday()
    {
        return $this->hasMany(\App\BirthdayWish::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function notifications()
    {
        return $this->hasMany(\App\CustomerNotification::class, 'user_id', 'customer_id'); // this matches the Eloquent model
    }

    public function reviews()
    {
        return $this->hasMany(\App\Review::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function subscribers()
    {
        return $this->hasOne(\App\Subscribers::class, 'email', 'customer_email'); // this matches the Eloquent model
    }

    public function promoUsage()
    {
        return $this->hasMany(\App\CardPromoCodeUsage::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function SslTransaction()
    {
        return $this->hasMany(\App\SslTransactionTable::class, 'customer_id', 'customer_id')->where('status', 1); // this matches the Eloquent model
    }

    public function branchTransactions()
    {
        return $this->hasMany(\App\TransactionTable::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function monthlyTransactionCount($from, $to)
    {
        return $this->hasMany(\App\TransactionTable::class, 'customer_id', 'customer_id')
            ->where('posted_on', '>=', $from)->where('posted_on', '<=', $to)->count();
    }

    public function singleSslTransaction()
    {
        return $this->hasOne(\App\SslTransactionTable::class, 'customer_id', 'customer_id'); // this matches the Eloquent model
    }

    public function selfDeletedReviews()
    {
        $count = Review::onlyTrashed()->where('customer_id', $this->customer_id)->where('admin_id', null)->count();

        return $count;
    }

    public function adminDeletedReviews()
    {
        $count = Review::onlyTrashed()->where('customer_id', $this->customer_id)->where('admin_id', '!=', null)->count();

        return $count;
    }

    public function scopeTop10Referrer($query)
    {
        return $query->where('reference_used', '>', 0)->orderBy('reference_used', 'DESC')->take(10)->get();
    }

    public function delete()
    {
        $this->birthday()->delete();
        $this->bonusRequest()->delete();
        $this->cardDelivery()->delete();
        $this->reviews()->delete();
        $this->notifications()->delete();
        $this->promoUsage()->delete();
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
