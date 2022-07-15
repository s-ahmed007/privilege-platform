<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class PushNotificationType
{
    //enum type for push notification
    const FROM_NEWSFEED = 111;
    const FROM_ADMIN = 999;
}
