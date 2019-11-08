<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfIsSuperAdmin
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
        if (user() && user()->isSuperAdmin) {
            return $next($request);
        }

        return abort(404);
    }
}
