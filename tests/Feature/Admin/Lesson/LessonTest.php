<?php

namespace Tests\Admin\Lesson;

use App\Models\Course;
use App\Models\CourseContainer;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LessonTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function lesson_list_page_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create();

        $response = $this->get(route('lessons.index', [
            'course' => $course->id,
            'module' => $module->id,
        ]));

        $response->assertSee('Lessons')
            ->assertSee('Add New Lesson')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function lesson_show_method_redirects_to_edit()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'hidden' => 0]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);

        $response = $this->get(route('lessons.show', [
            'course' => $module->course->id,
            'module' => $module->id,
            'lesson' => $lesson->id,
        ]));

        $response->assertStatus(302);;
    }
    /**
     * @test
     */
    public function lessons_for_modules_get_listed()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class, 5)->create();
        $lessons = factory(Lesson::class, 5)->create([
            'module_id' => $module[0]->id,
        ]);
        $otherLessons = factory(Lesson::class, 5)->create([
            'module_id' => $module[1]->id,
        ]);

        $response = $this->get(route('lessons.index', [
            'course' => $course->id,
            'module' => $module[0]->id,
        ]));
        $response->assertStatus(200);

        foreach ($lessons as $lesson) {
            $response->assertSee($lesson->title);
        }

        foreach ($otherLessons as $lesson) {
            $response->assertDontSee($lesson->title);
        }
    }

    /**
     * @test
     */
    public function add_lesson_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create();

        $response = $this->get(route('lessons.create', [
            'course' => $course,
            'module' => $module,
        ]));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function preview_lesson_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);

        $response = $this->call(
            'POST',
            "/admin/courses/{$course->id}/modules/{$module->id}/lessons/preview",
            [
                'course_id'   => $course->id,
                'module_id'   => $module->id,
                'description' => 'Test Description',
                'title'       => 'Test',
                'type'        => 'Lesson',
                'link'        => '',
            ]
        );

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_lesson()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);

        foreach (['Lesson', 'Link'] as $type) {
            $response = $this->post(
                route('lessons.store', ['course' => $module->course->id, 'module' => $module->id]), [
                    'title'       => 'some title',
                    'description' => 'some kind of description',
                    'link'        => 'http://test.com',
                    'status'      => 1,
                    'type'        => $type,
                ]
            );

            $this->assertDatabaseHas('lessons', [
                'title'       => 'some title',
                'description' => 'some kind of description',
                'link'        => 'http://test.com',
                'status'      => 1,
                'module_id'   => $module->id,
                'type'        => $type,
            ]);

            $response->assertRedirect(route('lessons.index',
                ['course' => $module->course->id, 'module' => $module->id]))
                ->assertSessionMissing('errors');
        }
    }

    /**
     * @test
     */
    public function edit_page_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'hidden' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);

        $response = $this->get(route('lessons.edit', [
            'course' => $module->course->id,
            'module' => $module->id,
            'lesson' => $lesson->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($lesson->title)
            ->assertSee($lesson->description);
    }

    /**
     * @test
     */
    public function successfully_edit_a_lesson()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'hidden' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);

        foreach (['Lesson', 'Link'] as $type) {
            $response = $this->put(
                route('lessons.update', [
                    'course' => $module->course->id,
                    'module' => $module->id,
                    'lesson' => $lesson->id,
                ]), [
                'title'       => 'new title',
                'description' => 'new description',
                'link'        => 'http://test.com',
                'status'      => 0,
                'type'        => $type,
            ]);

            $this->assertDatabaseHas('lessons', [
                'title'       => 'new title',
                'description' => 'new description',
                'link'        => 'http://test.com',
                'status'      => 0,
                'type'        => $type,
            ]);

            $response->assertRedirect(route('lessons.index',
                ['course' => $module->course->id, 'module' => $module->id]))
                ->assertSessionMissing('errors');
        }
    }

    /**
     * @test
     */
    public function successfully_store_drip_request()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'hidden' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        $lesson2 = factory(Lesson::class)->create(['module_id' => $module->id]);

        $drips = [
            $lesson->id  => 3,
            $lesson2->id => 12,
        ];

        $response = $this->post(route('lessons.drip'), [
            'drip_delay' => $drips,
        ]);

        $response->assertRedirect(route('modules.index', [
            'course' => $course->id,
        ]))
            ->assertSessionMissing('errors');

        $this->assertDatabaseHas('lessons', [
            'id'         => $lesson->id,
            'drip_delay' => 3,
        ]);

        $this->assertDatabaseHas('lessons', [
            'id'         => $lesson2->id,
            'drip_delay' => 12,
        ]);
    }
}
