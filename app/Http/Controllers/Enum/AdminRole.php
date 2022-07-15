<?php
/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 5/19/2018
 * Time: 12:08 PM.
 */

namespace App\Http\Controllers\Enum;

abstract class AdminRole
{
    //enum type for customer newsfeed
    const superadmin = 'superadmin';
    const admin = 'admin_sprt';
    const clientAdmin = 'clientAdmin';
    const internAdmin = 'internadmin';
    const adm_pass_change_stat = 1;
}
