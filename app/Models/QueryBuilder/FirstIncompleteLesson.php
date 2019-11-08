<?php

namespace App\Models\QueryBuilder;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

class FirstIncompleteLesson
{
    protected $user;
    protected $course;
    protected $ignoredLesson;

    public function __construct(User $user, Course $course, Lesson $ignoredLesson = null)
    {
        $this->user = $user;
        $this->course = $course;
        $this->ignoredLesson = $ignoredLesson;
    }

    public function get()
    {
        $lesson = $this->course->lessons()
            ->whereDoesntHave('subscriptions', function ($query) {
                $query->whereUserId($this->user->id);
            })
            ->where('modules.status', 1)
            ->where('lessons.status', 1)
            ->where('modules.type', 'Module')
            ->where('lessons.type', 'Lesson')
            ->orderBy('modules.order')
            ->orderBy('lessons.order');

        if ($this->ignoredLesson) {
            $lesson->where('lessons.id', '!=', $this->ignoredLesson->id);
        }

        // Look for a test if no lesson has been found.
        if (! $lesson = $lesson->first()) {
            return $this->getFirstUnsuccessfulTest();
        }

        return $lesson;
    }

    private function getFirstUnsuccessfulTest()
    {
        return $this->course->tests()
            ->enabled()
            ->whereDoesntHave('results', function ($query) {
                $query->whereUserId($this->user->id)->where('mark', '>', TEST_PASSING_MARK);
            })
            ->first();
    }
}