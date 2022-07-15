<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class NewsFeedType
{
    //enum type for customer newsfeed
    const post = 0;
    const review = 1;
    const visit = 2;
    const like = 3;
}
