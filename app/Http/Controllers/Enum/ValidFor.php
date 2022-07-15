<?php
/**
 * User: Sohel
 * Date: 5/06/2020
 * Time: 08:35 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class ValidFor
{
    //enum type of valid user for offer/deal
    const ALL_MEMBERS = 1;
    const PREMIUM_MEMBERS = 2;
}
