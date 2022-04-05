<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RestrictIpAddressMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // get IP list from config
        $allowedIps = Config::get('settings.allowed_ips');

        // check if all IP is allowed
        if (empty($allowedIps) || in_array('*', $allowedIps))
            return $next($request);

        // get client IP
        $clientIp = $this->getClientIP();

        // check if exist client IP and if it is allowed
        if (is_null($clientIp) || !in_array($clientIp, $allowedIps))
            return response("You are not allowed to access.", 403);

        return $next($request);
    }

    /**
     * Get client IP
     *
     * @return string|null
     */
    public function getClientIP() : ?string
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
            return  $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (array_key_exists('REMOTE_ADDR', $_SERVER))
            return $_SERVER["REMOTE_ADDR"];

        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
            return $_SERVER["HTTP_CLIENT_IP"];

        return null;
    }
}
