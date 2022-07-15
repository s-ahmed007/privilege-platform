<?php

namespace App;

use App\Http\Controllers\Enum\PartnerRequestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class BranchUser extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'branch_user';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'password', 'phone', 'role', 'active', 'f_token', 'jwt_token'];
    public $timestamps = false;
    protected $hidden = ['password'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($branchUser) {
            if ($branchUser->branchScanner) {
                $branchUser->branchScanner->delete();
            }
            foreach ($branchUser->offerRequest as $request) {
                $request->forceDelete();
            }
            foreach ($branchUser->notifications as $notification) {
                $notification->delete();
            }
            $branchUser->makeTransactionScannerIdNull($branchUser->id);
        });
    }

    public function branchScanner()
    {
        return $this->hasOne(\App\BranchScanner::class, 'branch_user_id', 'id'); // this matches the Eloquent model
    }

    public function offerRequest()
    {
        return $this->hasMany(\App\Wish::class, 'customer_id', 'id')
            ->where('partner_request_type', PartnerRequestType::offer_request);
    }

    public function makeTransactionScannerIdNull($id)
    {
        TransactionTable::where('branch_user_id', $id)->update(['branch_user_id' => null]);
    }

    public function notifications()
    {
        return $this->hasMany(\App\BranchUserNotification::class, 'branch_user_id', 'id');
    }

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

    public function delete()
    {
        try {
            return parent::delete();
        } catch (\Exception $e) {
        }
    }
}
