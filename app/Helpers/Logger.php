<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Spatie\HttpLogger\LogProfile;

class Logger implements LogProfile
{
    /**
     * A log profiler to log every API request to log file.
     */
    public function shouldLogRequest(Request $request): bool
    {
        $uri = $request->getPathInfo();
        if ($uri == '/logs') {
            return false;
        }
        if (explode('/', $uri)[1] == '_debugbar') {
            return false;
        }

        return in_array(strtolower($request->method()),
                        ['get', 'post', 'put', 'patch', 'delete']);
    }
}
