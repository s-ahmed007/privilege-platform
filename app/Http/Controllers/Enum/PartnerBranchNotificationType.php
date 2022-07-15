<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class PartnerBranchNotificationType
{
    //enum type for notification removal
    const TRANSACTION_REQUEST = 0;
    const LIKE_POST = 1;
    const REVIEW_POST = 2;
    const OFFER_AVAILED = 3;
    const DEAL_AVAILED = 4;
}
