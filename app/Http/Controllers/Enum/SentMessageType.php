<?php

namespace App\Http\Controllers\Enum;

abstract class SentMessageType
{
    const sms = 1;
    const push_notification = 2;

    const ALL_MEMBERS = 'All Members';
    const ALL_SCANNERS = 'All Scanners';
    const ALL_PREMIUMS = 'All Premium Members';
    const ALL_GUESTS = 'All Guest Members';
    const ALL_EXPIRED = 'All Expired Members';
    const ALL_ACTIVE = 'All Active Members';
    const ALL_INACTIVE = 'All Inactive Members';
    const ALL_EXPIRED_TRIAL = 'All Expired Trial Members';
    const ALL_EXPIRED_PREMIUM = 'All Expired Premium Members';
    const ALL_EXPIRING_MEMBERS = 'All Expiring Members';
}
