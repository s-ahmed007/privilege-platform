<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 10/13/2018
 * Time: 11:15 AM.
 */

namespace App\Http\Controllers\Enum;

abstract class SharerType
{
    //enum type for Liker type
    const customer = 1;
    const partner = 2;
    const anonymous = 3;
}
