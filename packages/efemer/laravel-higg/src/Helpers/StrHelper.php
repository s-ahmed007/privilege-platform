<?php

namespace Efemer\Higg\Helpers;

use Illuminate\Support\Str;

/**
 * String class extends helper class Str with string manipulation mehtods
 */
class StrHelper extends Str {

    /**
     * @param string $input camelCase string
     * @param string $glue joining char
     * @return string
     */
    function implodeCamelCase($input, $glue = '_') {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode($glue, $ret);
    }


}
