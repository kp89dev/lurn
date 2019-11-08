<?php

namespace Tests\Feature\Admin\Bonuses;

use App\Models\Bonus;
use App\Models\Course;

class ResourceControllerTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function index_bonusesAreListedAndPaginated()
    {
        $perPage = (new Bonus)->getPerPage();
        $courses = factory(Course::class, $perPage * 2)->create();

        foreach ($courses as $course) {
            $bonuses[] = factory(Bonus::class)->create(['course_id' => $course->id]);
        }

        $response = $this->get(route('bonuses.index'));

        foreach ($bonuses as $i => $bonus) {
            $i < $perPage
                ? $response->assertSee(e($bonus->course->title))
                : $response->assertDontSee(e($bonus->course->title));
        }
    }

    /**
     * @test
     */
    public function create_isAvailable()
    {
        $this->get(route('bonuses.create'))->assertSuccessful();
    }

    /**
     * @test
     */
    public function store_isValidated()
    {
        $this->withExceptionHandling();

        $this->post(route('bonuses.store'), [])
            ->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function store_newBonusGetsStored()
    {
        $course = factory(Course::class)->create();
        $data = [
            'course_id'       => $course->id,
            'points_required' => 1000,
        ];

        $this->post(route('bonuses.store'), $data);
        $this->assertDatabaseHas('bonuses', $data);
    }

    /**
     * @test
     */
    public function edit_isAvailable()
    {
        $course = factory(Course::class)->create();
        $bonus = factory(Bonus::class)->create(['course_id' => $course->id]);

        $this->get(route('bonuses.edit', $bonus))->assertSuccessful();
    }

    /**
     * @test
     */
    public function update_isValidated()
    {
        $this->withExceptionHandling();

        $course = factory(Course::class)->create();
        $bonus = factory(Bonus::class)->create(['course_id' => $course->id]);

        $this->put(route('bonuses.update', $bonus), [])
            ->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function update_newBonusDetailsGetStored()
    {
        $course = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();
        $bonus = factory(Bonus::class)->create(['course_id' => $course->id]);

        $data = [
            'course_id'       => $course2->id,
            'points_required' => 99999,
        ];

        $this->put(route('bonuses.update', $bonus), $data);
        $this->assertDatabaseHas('bonuses', $data);
    }

    /**
     * @test
     */
    public function destroy_softDeletesBonus()
    {
        $course = factory(Course::class)->create();
        $bonus = factory(Bonus::class)->create(['course_id' => $course->id]);

        $this->delete(route('bonuses.destroy', $bonus));
        $this->assertSoftDeleted('bonuses', ['id' => $bonus->id]);
    }
}