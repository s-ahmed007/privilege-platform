<?php

namespace Efemer\Higg\Helpers;

use Carbon\Carbon;

class CalendarHelper {

    public function __construct() {
    }

    function today($txOffset = '+0600') : Carbon {
        return new Carbon(date('Y-m-d'), new \DateTimeZone( $txOffset ));
    }

    function now($txOffset = '+0600') : Carbon {
        return new Carbon(null, new \DateTimeZone( $txOffset ));
    }



}
