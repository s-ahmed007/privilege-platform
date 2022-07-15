<?php

namespace Efemer\Royalty\Controllers;

class WebController extends BaseController {

    function ping() {
        return time();
    }

}
