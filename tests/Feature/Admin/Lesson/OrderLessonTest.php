<?php
namespace Tests\Admin\Lesson;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;

class OrderLessonTest extends \AdminLoggedInTestCase
{
    /**
     * @test
     */
    public function order_page_displays_items_correctly()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lessonsToOrder = factory(Lesson::class, 5)->create(['module_id' => $module->id]);
        $otherLessons = factory(Lesson::class, 5)->create();

        $response = $this->get(route('lessons.order', ['course' => $module->course_id, 'module' => $module->id]));

        $response->assertStatus(200);
        foreach ($lessonsToOrder as $lesson) {
            $response->assertSee(htmlentities($lesson->title, ENT_QUOTES));
        }

        foreach ($otherLessons as $lesson) {
            $response->assertDontSee(htmlentities($lesson->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function lessons_ordered_saved()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lessonsToOrder = factory(Lesson::class, 5)->create(['module_id' => $module->id]);

        $lessonIds = [];
        foreach ($lessonsToOrder as $l) {
            $lessonIds[] = $l->id;
        }

        shuffle($lessonIds);

        $response = $this->post(
            route('lessons.order.store', ['course' => $module->course_id, 'module' => $module->id]),
            [
                'ordered_lessons' => implode(" ", $lessonIds)
            ]);

        foreach ($lessonIds as $k => $lessonId) {
            $this->assertDatabaseHas('lessons', [
                'id' => $lessonId,
                'order' => $k
            ]);
        }


        $response->assertRedirect(route('lessons.index',
            ['course' => $module->course->id, 'module' => $module->id]))
            ->assertSessionMissing('errors');

    }
}
