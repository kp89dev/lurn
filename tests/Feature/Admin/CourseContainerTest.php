<?php

namespace Tests\Admin;

use App\Models\Course;
use App\Models\CourseContainer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CourseContainerTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function course_container_page_is_available()
    {
        $response = $this->get(route('course-containers.index'));

        $response->assertSee('Course Containers')
            ->assertSee('Add New Course Container')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function course_containers_get_listed()
    {
        $courseContainers = factory(CourseContainer::class, 10)->create([]);

        $response = $this->get(route('course-containers.index'));
        $response->assertStatus(200);

        foreach ($courseContainers as $courseContainer) {
            $response->assertSee(htmlentities($courseContainer->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function add_course_container_page_is_available()
    {
        $response = $this->get(route('course-containers.create'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_course_container()
    {
        $response = $this->post(
            url('admin/course-containers'), [
                'title'       => 'some title'
            ]
        );

        $this->assertDatabaseHas('course_containers', [
            'title'       => 'some title'
        ]);

        $response->assertRedirect(route('course-containers.index'))
                 ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function edit_page_can_be_accessed()
    {
        $existingContainer = factory(CourseContainer::class)->create();

        $response = $this->get(route('course-containers.edit', ['course_container' => $existingContainer->id]));

        $response->assertStatus(200)
                 ->assertSee($existingContainer->title);
    }

    /**
     * @test
     */
    public function successfully_edit_a_course_container()
    {
        $existingCourse = factory(CourseContainer::class)->create();

        $response = $this->put(
            route('course-containers.update', ['course' => $existingCourse->id]), [
                'title' => 'new title'
            ]
        );

        $this->assertDatabaseHas('course_containers', [
            'title' => 'new title'
        ]);

        $response->assertRedirect(route('course-containers.index'))
                 ->assertSessionMissing('errors');
    }
}
