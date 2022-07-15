<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class Constants
{
    //enum type for customer newsfeed
    const branch_transaction_count = 1;
    const refer_transaction_count = 2;
    const resend_time = 5;
    const notification_chunk = 500;
    const review_loadmore = 5;
}
