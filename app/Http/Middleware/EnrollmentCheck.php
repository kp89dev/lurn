<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;

class EnrollmentCheck
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
        if (! user_enrolled(request('course')) && ! user_is_admin()) {
            return redirect()->route('enroll', request('course')->slug);
        }

        return $next($request);
    }
}
