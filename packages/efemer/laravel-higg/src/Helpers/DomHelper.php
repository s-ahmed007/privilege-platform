<?php

namespace Efemer\Higg\Helpers;

use Html;

class DomHelper {

    // TODO add support for multiple favicon variations
    function favicon($url = null, $attributes = [], $secure = null){
        $url = 'favicon.ico';
        return Html::favicon($url);
    }

}
