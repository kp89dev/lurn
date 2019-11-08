<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnrollmentCheck;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonSubscriptions;
use App\Models\LessonUserNote;
use App\Models\Test;
use App\Models\UserCourse;
use Illuminate\Http\JsonResponse;

class ClassroomController extends Controller
{

    public $completeCourse = false;

    /**
     * @param Course $course
     * @return JsonResponse
     */
    public function notes(Course $course)
    {
        $lesson = $course->lessons()->find(request('lesson'));

        if ($this->cantTakeNotes($course, $lesson)) {
            return response()->json(['status' => false]);
        }

        $response = LessonUserNote::updateOrCreate([
            'user_id'   => user()->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ], request()->only('notes'));

        return response()->json(['status' => (bool) $response]);
    }

    /**
     * @param Course $course
     * @param        $lesson
     * @return bool
     */
    private function cantTakeNotes(Course $course, $lesson)
    {
        return ! $lesson || user_isnt_enrolled($course);
    }

    /**
     * @param Course $course
     * @return JsonResponse
     */
    public function completeLesson(Course $course)
    {
        $lesson = $course->lessons()->find(request('lesson'));

        if ($this->cantCompleteLesson($course, $lesson)) {
            return response()->json(['status' => false, 'complete' => $this->completeCourse]);
        }

        $response = $this->setLessonComplete($lesson);

        return response()->json(['status' => $response, 'complete' => $this->completeCourse]);
    }

    /**
     * @param Course $course
     * @param        $lesson
     * @return bool
     */
    public function cantCompleteLesson(Course $course, $lesson)
    {
        return ! $lesson || user_isnt_enrolled($course);
    }

    /**
     * @param Lesson $lesson
     * @return bool
     */
    public function setLessonComplete($lesson)
    {
        if (user_completed($lesson)) {
            return true;
        }

        $setLesson = LessonSubscriptions::updateOrCreate([
            'user_id'   => user()->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->completeCourse($lesson);

        return (bool) $setLesson;
    }

    private function completeCourse($lesson)
    {
        $progress = $lesson->module->course->getProgress();

        if ($progress < 100) {
            return;
        }

        $userCourse = (new UserCourse)->whereUserId(user()->id)
            ->whereCourseId($lesson->module->course_id)
            ->first();

        if (! is_null($userCourse)) {
            $userCourse->markCourseAsCompleted();
        }

        $this->completeCourse = true;
    }
}
