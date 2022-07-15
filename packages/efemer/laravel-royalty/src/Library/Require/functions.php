<?php

function globalUserId() {
    if (\Auth::check()) {
        return Auth::user()->ID;
    }
    return 0;
}

function rand_str_with_prefix($prefix = '', $length = 10) {
    $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    return $prefix . $randomString;
}
