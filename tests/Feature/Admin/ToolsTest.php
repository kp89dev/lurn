<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\CourseTool;
use App\Models\Course;

class ToolsTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function tools_page_available()
    {
        $response = $this->get(route('tools.index'));
        $response->assertStatus(200)
            ->assertSee('Tools');
    }

    /**
     * @test
     */
    public function tool_is_listed_on_tools_page()
    {
        $course = factory(Course::class)->create();
        $toolsItems = factory(CourseTool::class, 5)->create(['course_id' => $course->id]);

        $response = $this->get(route('tools.index'));

        $response->assertStatus(200);

        foreach($toolsItems as $tools) {
            $response->assertSee(htmlspecialchars($tools->tool_name, ENT_QUOTES))
                ->assertSee(htmlspecialchars($tools->course->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function tool_creation_page_available()
    {
        $course = factory(Course::class)->create();
        $response = $this->get(route('tools.create'));

        $response->assertStatus(200)
            ->assertSee(htmlspecialchars($course->title, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function tool_edit_page_available()
    {
        $course = factory(Course::class)->create();
        $courseTool = factory(CourseTool::class)->create(['course_id' => $course->id]);

        $response = $this->get(route('tools.edit', ['id' => $courseTool->id]));

        $response->assertStatus(200)
            ->assertSee(htmlspecialchars($course->title, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function tool_is_created()
    {
        $course = factory(Course::class)->create();
        $courseTool = factory(CourseTool::class)->create(['course_id' => $course->id]);

        $response = $this->post(route('tools.store', [
            'course_id' => $courseTool->course_id,
            'tool_name' => $courseTool->tool_name,
        ]));

        $this->assertDatabaseHas('course_tools', [
            'course_id' => $courseTool->course_id,
            'tool_name' => $courseTool->tool_name
        ]);

        $response->assertRedirect(route('tools.index'));
    }

    /**
     * @test
     */
    public function tool_is_edited()
    {
        $courseTool = factory(CourseTool::class)->create();

        $courseToolB = factory(CourseTool::class)->create();

        $this->put(route('tools.update', [
            'id'        => $courseTool->id,
            'course_id' => $courseToolB->course_id,
            'tool_name' => $courseToolB->tool_name
        ]));

        $this->assertDatabaseHas('course_tools', [
            'id'        => $courseTool->id,
            'course_id' => $courseToolB->course_id,
            'tool_name' => $courseToolB->tool_name
        ]);
    }

    /**
     * @test
     */
    public function tool_gets_deleted()
    {
        $tool = factory(CourseTool::class)->create();

        $this->delete(route('tools.destroy', $tool->id));
        $this->assertDatabaseMissing('course_tools', ['id' => $tool->id]);
    }

}   