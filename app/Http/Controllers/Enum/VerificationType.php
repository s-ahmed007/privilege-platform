<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 10/13/2018
 * Time: 11:15 AM.
 */

namespace App\Http\Controllers\Enum;

abstract class VerificationType
{
    //enum type for delivery type
    const reset_password = 1;
    const email_verification = 2;
    const phone_verification = 3;
    const spot_purchase = 4;
}
