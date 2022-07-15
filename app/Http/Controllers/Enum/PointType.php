<?php

namespace App\Http\Controllers\Enum;

abstract class PointType
{
    const refer_point = 1;
    const rating_point = 2;
    const review_point = 3;
    const profile_completion_point = 4;
    const referred_by_point = 5;
    const deal_refund_point = 6;
    const deal_redeem_point = 7;
}
