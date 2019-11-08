<?php

namespace Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;

class ExternalOnboardingControllerTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function categories_page_available()
    {
        $categories = factory(Category::class, 5);
        $response = $this->get(route('onboarding.index'));

        $response->assertStatus(200);

        foreach($categories as $c) {
            $response->assertSee($c->name);
        }
    }

    /**
     * @test
     */
    public function interests_save_correctly()
    {
        $categories = factory(Category::class, 5)->create();
        $this->post(route('onboarding.interests'),[
            'categories' => [
                $categories[0]->id,
                $categories[1]->id
            ]
        ]);

        $this->assertDatabaseHas('category_user', [
            'category_id' => $categories[0]->id,
            'user_id' => $this->user->id
            ]
        );

        $this->assertDatabaseHas('category_user', [
                'category_id' => $categories[0]->id,
                'user_id' => $this->user->id
            ]
        );

        $this->assertDatabaseMissing('category_user', [
                'category_id' => $categories[2]->id,
                'user_id' => $this->user->id]
        );
    }

    /**
     * @test
     */
    public function course_choice_available()
    {
        $categories = factory(Category::class, 5)->create();

        foreach($categories as $category) {
            $this->user->categories()->attach($category->id);
        }

        $response = $this->get(route('onboarding.courses'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function demo_page_available()
    {
        $course = factory(Course::class)->create();
        $this->get(route('onboarding.demo', $course))->assertStatus(200);
    }

    /**
     * @test
     */
    public function enrollment_occurs_appropriately()
    {
        $courses = factory(Course::class, 6)->create(['free' => 1]);

        $this->post(route('onboarding.enroll'), [
            'courses' => [
                $courses[0]->id,
                $courses[1]->id
            ]
        ]);

        $this->assertTrue(user_enrolled($courses[0]), $this->user);
        $this->assertTrue(user_enrolled($courses[1]), $this->user);
    }

    /**
     * @test
     */
    public function remote_register_page_available()
    {
        $response = $this->get(route('onboarding.signup'));
        $response->assertStatus(200)
            ->assertSessionHas('redirectTo');
    }
}
