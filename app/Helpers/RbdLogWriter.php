<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\HttpLogger\LogWriter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RbdLogWriter implements LogWriter
{
    /**
     * A logger writer to log every API request to log file.
     */
    public function logRequest(Request $request): void
    {
        $token = $request->bearerToken() ? 'Yes' : 'No';

        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        $ip = $request->ip();

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $files = array_map(function ($file) {
            if ($file instanceof UploadedFile) {
                return $file->getClientOriginalName();
            }
        }, iterator_to_array($request->files));

        $message = "{$method} {$uri} - Token: {$token} - Body: {$bodyAsJson} - Files: ".implode(', ', $files).'- ip: '.$ip;

        Log::info($message);
    }
}
