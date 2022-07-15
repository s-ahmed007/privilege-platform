<?php

namespace App\Http\Controllers\Enum;

abstract class CustomerType
{
    const card_holder = 1;
    const virtual_card_holder = 2;
    const trial_user = 3;
}
