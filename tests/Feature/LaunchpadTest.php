<?php
namespace Tests\Feature;

use App\Models\Course;

class LaunchpadTest extends \LoggedInTestCase
{

    /**
     * @test
     */
    public function launchpad_page_returns_200()
    {
        factory(Course::class)->create(['slug' => 'inbox-blueprint']);

        $this->get(route('launchpad'))
            ->assertStatus(200);
    }
}
