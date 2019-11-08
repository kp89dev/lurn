<?php
namespace Tests\Admin\Homepage;

use App\Models\CourseFeature;
use App\Models\Course;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomepageTest extends \SuperAdminLoggedInTestCase
{

    use DatabaseTransactions;

    /**
     * @test
     */
    public function homepage_settings_page_is_available()
    {
        factory(CourseFeature::class, 4)->create(['free_bootcamp' => 0]);
        factory(CourseFeature::class, 3)->create(['free_bootcamp' => 1]);

        $response = $this->get(route('homepage.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function set_featured_courses()
    {
        $response = $this->post(route('homepage.store-featured'), [
            'featured1' => 1,
            'featured2' => 2,
            'featured3' => 3,
            'featured4' => 4,
            'freeBootcamp' => 0
        ]);

        $response->assertRedirect()
            ->assertSessionMissing('errors');

        for ($i = 1; $i <= 4; $i++) {
            $this->assertDatabaseHas('course_features', [
                'course_id' => $i,
                'order' => $i,
                'free_bootcamp' => 0
            ]);
        }

        self::assertEquals(4, CourseFeature::count());
    }

    /**
     * @test
     */
    public function set_freebootcamp_coruses()
    {
        $response = $this->post(route('homepage.store-featured'), [
            'featured1' => 1,
            'featured2' => 2,
            'featured3' => 3,
            'freeBootcamp' => 1
        ]);

        $response->assertRedirect()
            ->assertSessionMissing('errors');

        for ($i = 1; $i <= 3; $i++) {
            $this->assertDatabaseHas('course_features', [
                'course_id' => $i,
                'order' => $i,
                'free_bootcamp' => 1
            ]);
        }

        self::assertEquals(3, CourseFeature::count());
    }

    /**
     * @test
     */
    public function purchasable_change_featured_course()
    {
        $course = factory(Course::class)->create();
        $response = $this->post(route('homepage.store-featured'), [
            'featured1' => $course->id,
            'freeBootcamp' => 0
        ]);

        $response->assertRedirect()
            ->assertSessionMissing('errors');

        $this->assertDatabaseHas('course_features', [
            'course_id' => $course->id,
        ]);

        $responseUpdate = $this->put(route('courses.update', $course), [
            'title' => 'new title',
            'description' => 'new description',
            'snippet' => 'some sort of snippet',
            'status' => 1,
            'purchasable' => 0,
            'is_product_id' => 200,
            'is_subscription_product_id' => 0,
            'is_account' => 'JP126',
            'price' => 1500,
            'subscription' => 1,
            'sendlaneAccount' => 10,
            'client_id' => '12345',
            'client_secret' => 'secret',
            'url' => 'http://google.com',
            'recommended1' => 'none',
            'recommended2' => 'none',
            'recommended3' => 'none',
            'recommended4' => 'none',
            'confirm_after' => 'M',
        ]);

        $responseUpdate->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'status' => 1,
            'purchasable' => 0,
        ]);

        $this->assertDatabaseMissing('course_features', [
            'course_id' => $course->id,
        ]);
    }
}
