<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class SSLPaymentType
{
    //enum type for SSL payment type
    const MEMBERSHIP = 1;
    const DONATION = 2;
    const VOUCHER = 3;
}
