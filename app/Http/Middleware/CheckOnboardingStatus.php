<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Closure;

class CheckOnboardingStatus
{
    protected $request;
    protected $onboardingModule;
    protected $onboardingLesson;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;
        $this->onboardingModule = $this->request->course->getOnboardingModule();
        $this->onboardingLesson = $this->getOnboardingLesson();

        if ($this->shouldRedirect()) {
            return redirect()->route('lesson', [
                'course' => $this->request->course->slug,
                'module' => $this->onboardingModule->slug,
                'lesson' => $this->onboardingLesson->slug,
            ]);
        }

        return $next($request);
    }

    /**
     * Returns the onboarding lesson, if it can find it.
     *
     * @return null|Lesson
     */
    protected function getOnboardingLesson()
    {
        if (! $this->onboardingModule instanceof Module) {
            return null;
        }

        return $this->onboardingModule->lessons()->enabled()->first();
    }

    /**
     * Checks if we should redirect the user to the onboarding lesson.
     *
     * @return bool
     */
    protected function shouldRedirect()
    {
        $currentLesson = $this->request->lesson;

        return ! $this->request->course->userIsBoarded()
            && (! $currentLesson instanceof Lesson || $currentLesson->id != $this->onboardingLesson->id);
    }
}
