<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 10/13/2018
 * Time: 11:15 AM.
 */

namespace App\Http\Controllers\Enum;

abstract class TransactionRequestStatus
{
    //enum type for Liker type
    const PENDING = 0;
    const ACCEPTED = 1;
    const DECLINED = 2;
}
