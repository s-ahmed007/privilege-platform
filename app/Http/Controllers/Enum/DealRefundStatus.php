<?php

namespace App\Http\Controllers\Enum;

abstract class DealRefundStatus
{
    const REQUESTED = 0;
    const ACCEPTED = 1;
    const REJECTED = 2;
}
