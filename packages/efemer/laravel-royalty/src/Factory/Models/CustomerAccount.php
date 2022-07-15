<?php

namespace Efemer\Royalty\Factory\Models;

use Efemer\Higg\Factory\Handlers\FormHandler;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CustomerAccount extends RoyaltyModel
    implements JWTSubject, AuthenticatableContract,
    AuthorizableContract, CanResetPasswordContract {

    use Authorizable, CanResetPassword, MustVerifyEmail;

	protected $table = 'customer_account';
	public $alias = 'customer_account';
	protected $primaryKey = 'customer_id';

	protected $fields = [

		'customer_id' => [ 'cast' => CAST_INTEGER ],
		'customer_serial_id' => [ 'cast' => CAST_INTEGER ],
		'customer_username' => [ 'cast' => CAST_INTEGER ],
		'password' => [ 'cast' => CAST_INTEGER ],
		'pin' => [ 'cast' => CAST_INTEGER ],
		'moderator_status' => [ 'cast' => CAST_INTEGER ],
		'isSuspended' => [ 'cast' => CAST_INTEGER ],
		'platform' => [ 'cast' => CAST_INTEGER ],

	];


}
