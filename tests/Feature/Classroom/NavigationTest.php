<?php

namespace Tests\Feature\Classroom;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Test;
use Carbon\Carbon;

class NavigationTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function lesson_get_index_works()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'type' => 'Module']);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'type' => 'Lesson',
            'status' => 1,
            'order' => 1,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'type' => 'Lesson',
            'status' => 1,
            'order' => 2,
        ]);

        $this->assertEquals($lesson->getIndex(), 1);
        $this->assertEquals($nextLesson->getIndex(), 2);
    }

    /**
     * @test
     */
    public function lesson_getNext_works()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
        ]);

        $this->assertEquals(($lesson->getNext())->id, $nextLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getNext_works_when_order_and_id_differ()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
            'id'    => 1
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
            'id'    => 25,
        ]);

        $breakingLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 4,
            'id'    => 10,
        ]);

        $this->assertEquals($lesson->getNext()->id, $nextLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getNext_works_with_test()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $test = factory(Test::class)->create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id
        ]);

        $this->assertEquals(($lesson->getNext())->id, $test->id);
    }

    /**
     * @test
     */
    public function lesson_getNext_works_when_next_disabled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
            'status' => 0,
        ]);

        $thirdLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 3,
        ]);

        $this->assertEquals($lesson->getNext()->id, $thirdLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getNext_works_crossing_modules()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create([
            'course_id' => $course->id,
            'status' => 1,
            'order' => 1,
        ]);

        $module2 = factory(Module::class)->create([
            'course_id' => $course->id,
            'status' => 1,
            'order' => 2,
        ]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $lesson2 = factory(Lesson::class)->create([
            'module_id' => $module2->id,
            'status' => 1,
            'order' => 1,
        ]);

        $this->assertEquals($lesson->getNext()->id, $lesson2->id);
    }


    /**
     * @test
     */
    public function lesson_getPrev_works()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $this->assertEquals($lesson->getPrevious()->id, $nextLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getPrev_works_when_order_differs_from_id()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 5,
            'id'    => 4,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 4,
            'id'    => 14,
        ]);

        $breakingLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 6,
            'id'    => 2,
        ]);

        $this->assertEquals($lesson->getPrevious()->id, $nextLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getPrev_works_with_test()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);

        $priorLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $test = factory(Test::class)->create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $priorLesson->id
        ]);

        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
        ]);

        $this->assertEquals(($lesson->getPrevious())->id, $test->id);
    }

    /**
     * @test
     */
    public function lesson_getPrev_works_when_next_disabled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 3,
        ]);

        $nextLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
            'status' => 0,
        ]);

        $thirdLesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $this->assertEquals($lesson->getPrevious()->id, $thirdLesson->id);
    }

    /**
     * @test
     */
    public function lesson_getPrev_works_crossing_modules()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create([
            'course_id' => $course->id,
            'status' => 1,
            'order' => 2,
        ]);

        $module2 = factory(Module::class)->create([
            'course_id' => $course->id,
            'status' => 1,
            'order' => 1,
        ]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
        ]);

        $lesson2 = factory(Lesson::class)->create([
            'module_id' => $module2->id,
            'status' => 1,
            'order' => 1,
        ]);

        $this->assertEquals($lesson->getPrevious()->id, $lesson2->id);
    }


    public function next_button_adheres_to_drip_rules()
    {
        $course = factory(Course::class)->create(['status' => 1, 'drip' => 1]);
        $module = factory(Module::class)->create([
            'course_id' => $course->id,
            'status' => 1,
        ]);

        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 1,
            'drip_delay' => 0,
        ]);

        $lesson2 = factory(Lesson::class)->create([
            'module_id' => $module->id,
            'status' => 1,
            'order' => 2,
            'drip_delay' => 1,
        ]);

        $this->user->enroll($course);

        $this->assertNull($lesson->getNext());
    }
}
