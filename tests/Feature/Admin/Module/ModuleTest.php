<?php

namespace Tests\Admin\Module;

use App\Models\Course;
use App\Models\CourseContainer;
use App\Models\Module;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModuleTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function module_list_page_is_available()
    {

        $course = factory(Course::class)->create();
        $response = $this->get(route('modules.index', ['course' => $course->id ]));

        $response->assertSee('Modules')
            ->assertSee('Add New Module')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function modules_get_listed()
    {
        $course = factory(Course::class)->create();
        $modules = factory(Module::class, 2)->create([
            'course_id' => $course->id
        ]);

        $otherCourse = factory(Course::class)->create();
        $otherModules = factory(Module::class, 2)->create([
            'course_id' => $otherCourse->id
        ]);

        $response = $this->get(route('modules.index', ['course' => $course->id]));
        $response->assertStatus(200);

        foreach ($modules as $module) {
            $response->assertSee($module->title);
        }

        foreach ($otherModules as $module) {
            $response->assertDontSee($module->title);
        }
    }

    /**
     * @test
     */
    public function add_module_page_is_available()
    {
        $course = factory(Course::class)->create();

        $response = $this->get(route('modules.create', ['course' => $course]));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_module_type_module()
    {
        $course = factory(Course::class)->create();

        $response = $this->post(
            route('modules.store', ['course' => $course->id]), [
                'title'       => 'some title',
                'description' => 'some kind of description',
                'status'      => 1,
                'hidden'      => 1
            ]
        );

        $this->assertDatabaseHas('modules', [
            'title'       => 'some title',
            'description' => 'some kind of description',
            'status'      => 1,
            'course_id'   => $course->id,
            'hidden'      => 1,

        ]);

        $response->assertRedirect(route('modules.index', ['course' => $course->id]))
                 ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function successfully_add_a_new_module_type_link()
    {
        $course = factory(Course::class)->create();

        $response = $this->post(
            route('modules.store', ['course' => $course->id]), [
                'title'       => 'some title',
                'link'        => 'http://test.com',
                'status'      => 1,
                'hidden'      => 1
            ]
        );

        $this->assertDatabaseHas('modules', [
            'title'       => 'some title',
            'link'        => 'http://test.com',
            'status'      => 1,
            'course_id'   => $course->id,
            'hidden'      => 1,
        ]);

        $response->assertRedirect(route('modules.index', ['course' => $course->id]))
            ->assertSessionMissing('errors');
    }
    /**
     * @test
     */
    public function edit_page_can_be_accessed()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'hidden' => 1]);

        $response = $this->get(route('modules.edit', [
            'course' => $module->course->id,
            'module' => $module->id
        ]));

        $response->assertStatus(200)
            ->assertSee($module->title)
            ->assertSee($module->description);
    }

    /**
     * @test
     */
    public function successfully_edit_a_module_type_module()
    {
        $module = factory(Module::class)->create(['status' => 1, 'hidden' => 1]);
        $course = factory(Course::class)->create();

        $response = $this->put(
            url('admin/courses/' . $course->id . '/modules/' . $module->id), [
            'title'               => 'new title',
            'description'         => 'new description',
            'status'              => 0,
            'hidden'              => 0
        ]);

        $this->assertDatabaseHas('modules', [
            'title'               => 'new title',
            'description'         => 'new description',
            'status'              => 0,
            'hidden'              => 0
        ]);

        $response->assertRedirect(route('modules.index', ['course' => $course->id]))
                 ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function successfully_edit_a_module_type_link()
    {
        $module = factory(Module::class)->create(['status' => 1, 'hidden' => 1]);
        $course = factory(Course::class)->create();

        $response = $this->put(
            url('admin/courses/' . $course->id . '/modules/' . $module->id), [
            'title'               => 'new title',
            'link'        => 'http://test.com',
            'status'              => 0,
            'hidden'              => 0
        ]);

        $this->assertDatabaseHas('modules', [
            'title'               => 'new title',
            'link'        => 'http://test.com',
            'status'              => 0,
            'hidden'              => 0
        ]);

        $response->assertRedirect(route('modules.index', ['course' => $course->id]))
                 ->assertSessionMissing('errors');
    }
}
