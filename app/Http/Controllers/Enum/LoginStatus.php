<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class LoginStatus
{
    //enum type for customer newsfeed
    const logged_out = 0;
    const logged_in = 1;
    const kicked = 2;
}
