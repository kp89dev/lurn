<?php

namespace App\Http\Middleware;

use Closure;

class SetAffiliateCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $code = $request->input('ref') ?: '';
        $slug = $request->input('landing') ?: '';

        if (!$request->hasCookie('cpa_referral_code') && $code) {
            $ts = \Carbon\Carbon::now()->toDateTimeString();

            $cookie = cookie('cpa_referral_code', "{$code}|{$slug}|{$request->server('HTTP_REFERER')}|{$ts}", 86400 * 30);

            return $next($request)->withCookie($cookie);
        }

        return $next($request);
    }
}
