<?php
namespace Tests\Admin\Lesson;

use App\Models\Course;
use App\Models\Module;

class OrderModuleTest extends \AdminLoggedInTestCase
{
    /**
     * @test
     */
    public function order_page_displays_items_correctly()
    {
        $course = factory(Course::class)->create();
        $modulesToOrder = factory(Module::class, 5)->create(['course_id' => $course->id]);
        $otherModules = factory(Module::class, 5)->create();

        $response = $this->get(route('modules.order', ['course' => $course->id]));

        $response->assertStatus(200);
        foreach ($modulesToOrder as $module) {
            $response->assertSee($module->title);
        }

        foreach ($otherModules as $module) {
            $response->assertDontSee($module->title);
        }
    }

    /**
     * @test
     */
    public function modules_ordered_saved()
    {
        $course = factory(Course::class)->create();
        $modulesToOrder = factory(Module::class, 5)->create(['course_id' => $course->id]);

        $moduleIds = [];
        foreach ($modulesToOrder as $l) {
            $moduleIds[] = $l->id;
        }

        shuffle($moduleIds);

        $response = $this->post(
            route('modules.order.store', ['course' => $course->id]),
            [
                'ordered_modules' => implode(" ", $moduleIds)
            ]);

        foreach ($moduleIds as $k => $moduleId) {
            $this->assertDatabaseHas('modules', [
                'id' => $moduleId,
                'order' => $k
            ]);
        }

        $response->assertRedirect(route('modules.index', ['course' => $course->id]))
            ->assertSessionMissing('errors');

    }
}
