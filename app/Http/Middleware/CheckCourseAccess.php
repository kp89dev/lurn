<?php

namespace App\Http\Middleware;

use Closure;

class CheckCourseAccess
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
        // Check if the subscription is expired.
        $subscription = user() ? user()->enrolled($request->course) : null;

        if ($subscription && $subscription->expired) {
            return redirect()->route('access.denied', $request->course->slug);
        }

        return $next($request);
    }
}
