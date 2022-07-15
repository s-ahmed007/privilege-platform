<?php

namespace App\Http\Controllers\Enum;

abstract class UserListType
{
    const all = 1;
    const guest = 2;
    const trial = 3;
    const premium = 4;
}
