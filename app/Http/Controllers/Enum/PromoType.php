<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 12/02/2018
 * Time: 12:30 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class PromoType
{
    //enum type for delivery type
    const TRIAL = 0;
    const RENEW = 1;
    const CARD_PURCHASE = 2;
    const UPGRADE = 3;
    const ALL = 4;
}
