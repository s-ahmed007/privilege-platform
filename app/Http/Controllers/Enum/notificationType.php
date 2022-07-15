<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class notificationType
{
    //enum type for notification removal
    const like_review = 1;
    const transaction = 3;
    const partner_follow = 4;
    const reply_review = 6;
    const customer_follow = 8;
    const follow_accept = 9;
    const refer = 10;
    const reward = 11;
    const deal = 12;
    const deal_refund_rejected = 13;
}
