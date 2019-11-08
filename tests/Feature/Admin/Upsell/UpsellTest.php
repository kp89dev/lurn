<?php

namespace Tests\Admin\Upsell;

use App\Models\Course;
use App\Models\CourseContainer;
use App\Models\CourseInfusionsoft;
use App\Models\CourseSendlane;
use App\Models\CourseUpsell;
use App\Models\Sendlane;
use App\Services\Sendlane\Sendlane as SendlaneService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpsellTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function upsell_page_is_available()
    {
        $response = $this->get(route('upsells.index'));

        $response->assertSee('Upsells')
            ->assertSee('Add New Upsell')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function upsells_get_listed()
    {
        $courses = factory(Course::class, 2)->create();
        $courseInfusionsoft = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'upsell'    => 1
        ]);

        factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseInfusionsoft->id,
            'succeeds_course_id'     => $courses[1]->id
        ]);

        $response = $this->get(route('upsells.index'));
        $response->assertStatus(200)
                 ->assertSeeText($courses[0]->title)
                 ->assertSee($courses[1]->title);

    }

    /**
     * @test
     */
    public function add_upsell_page_is_available()
    {
        $course = factory(Course::class)->create();

        $response = $this->get(route('upsells.create'));
        $response->assertStatus(200)
                 ->assertSee($course->title);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_upsell()
    {
        $courses = factory(Course::class, 2)->create();

        $response = $this->post(
            route('upsells.store'), [
                'course_id'              => $courses[0]->id,
                'succeeds_course_id'     => $courses[1]->id,
                'status'                 => 1,
                'html'                   => 'some html',
                'css'                    => 'some css rules',
                'is_product_id'          => 100,
                'is_account'             => 'JP126',
                'price'                  => 1497,
                'subscription'           => 1
            ]
        );

        $this->assertDatabaseHas('course_upsells', [
            'succeeds_course_id'     => $courses[1]->id,
            'status'                 => 1,
            'html'                   => 'some html',
            'css'                    => 'some css rules',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'price'          => 1497,
            'subscription'   => 1,
            'is_product_id'  => 100,
            'is_account'     => 'JP126'
        ]);

        $response->assertRedirect(route('upsells.index'))
                 ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function upsell_edit_page_can_be_accessed()
    {
        $courses = factory(CourseContainer::class, 2)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'upsell'    => 1
        ]);
        $existingUpsell = factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseIS->id,
            'succeeds_course_id'     => $courses[1]->id
        ]);

        $response = $this->get(route('upsells.edit', ['upsell' => $existingUpsell->id]));
        $response->assertStatus(200)
                 ->assertSee($existingUpsell->html)
                 ->assertSee($existingUpsell->css);
    }

    /**
     * @test
     */
    public function successfully_edit_an_upsell()
    {
        $courses = factory(CourseContainer::class, 2)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'upsell'    => 1
        ]);
        $existingUpsell = factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseIS->id,
            'succeeds_course_id'     => $courses[1]->id
        ]);

        $response = $this->put(
            route('upsells.update', $existingUpsell->id), [
                'course_id' => $courses[0]->id,
                'succeeds_course_id'     => $courses[1]->id,
                'status'                 => 1,
                'html'                   => 'some html',
                'css'                    => 'some css rules',
                'is_product_id'          => 100,
                'is_account'             => 'JP126',
                'price'                  => 1497,
                'subscription'           => 1
            ]);

        $response->assertSessionMissing('errors')
                 ->assertRedirect(route('upsells.edit', ['upsell' => $existingUpsell->id]));

        $this->assertDatabaseHas('course_upsells', [
            'succeeds_course_id'     => $courses[1]->id,
            'status'                 => 1,
            'html'                   => 'some html',
            'css'                    => 'some css rules',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'course_id'      => $courses[0]->id,
            'price'          => 1497,
            'subscription'   => 1,
            'is_product_id'  => 100,
            'is_account'     => 'JP126'
        ]);
    }
}
