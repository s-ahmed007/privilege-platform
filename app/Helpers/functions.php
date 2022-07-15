<?php
/**
 * laravel factory functions overrides.
 * @author Imran
 */

use Illuminate\Contracts\Routing\UrlGenerator;

function asset($path, $secure = null)
{
    if (is_null($secure)) {
        $secure = env('APP_SECURE');
    }

    return app('url')->asset($path, $secure);
}

function url($path = null, $parameters = [], $secure = null)
{
    if (is_null($secure)) {
        $secure = env('APP_SECURE');
    }
    if (is_null($path)) {
        return app(UrlGenerator::class);
    }

    return app(UrlGenerator::class)->to($path, $parameters, $secure);
}
