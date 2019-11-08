<?php

namespace App\Http\Middleware;

use Closure;

class AdminRoleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param                           $module
     * @param array                     $abilities
     * @return mixed
     */
    public function handle($request, Closure $next, $module, ...$abilities)
    {
        if (! user()->hasAdminAccess($module, $abilities)) {
            return redirect(route('admin'))->with('alert-danger', 'You are not allowed to access that page.');
        }

        return $next($request);
    }
}
