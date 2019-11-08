<?php

namespace App\Http\Middleware;

use App\Models\Lesson;
use App\Models\LessonSubscriptions;
use Closure;
use Illuminate\Http\Request;

class RedirectIfResourceIsLink
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach (['module', 'lesson'] as $resource) {
            $resource = $request->$resource;

            if ($resource && $resource->type == 'Link') {
                return $this->markCompletedAndRedirect($resource);
            }
        }

        return $next($request);
    }

    protected function markCompletedAndRedirect($resource)
    {
        if ($resource instanceof Lesson && user()) {
            LessonSubscriptions::updateOrCreate([
                'user_id'   => user()->id,
                'lesson_id' => $resource->id,
            ]);
        }

        return redirect($resource->link);
    }
}
