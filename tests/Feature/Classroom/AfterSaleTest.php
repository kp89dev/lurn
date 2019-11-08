<?php
namespace Tests\Feature\Classroom;

use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\CourseUpsell;
use App\Models\CourseUpsellToken;

class AfterSaleTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function user_accidentally_accesses_after_sale_page()
    {
        $course = factory(Course::class)->create();

        $response = $this->get(route('enroll.after-sale', compact('course')));
        $response->assertRedirect(route('enroll', compact('course')));
    }

    /**
     * @test
     */
    public function on_no_upsells_defined_user_sees_thank_you_page()
    {
        $course = factory(Course::class)->create();
        $this->user->courses()->attach($course);
        $this->app['session']->put('recent_purchase_id', $course->id);

        $response = $this->get(route('enroll.after-sale', compact('course')));
        $response->assertStatus(200)
                 ->assertViewIs('enroll.thank-you');
    }

    /**
     * @test
     */
    public function on_existing_upsells_but_bought_already_returns_thank_you_page()
    {
        $courses = factory(Course::class, 3)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'upsell'    => 1
        ]);
        factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseIS,
            'succeeds_course_id'     => $courses[1]->id
        ]);

        #just filling tables  with unrelated data
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[2]->id,
            'upsell'    => 1
        ]);
        factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseIS,
            'succeeds_course_id'     => $courses[2]->id
        ]);

        $this->user->courses()->attach($courses);

        $this->app['session']->put('recent_purchase_id', $courses[1]->id);

        $response = $this->get(route('enroll.after-sale', ['course' => $courses[1]]));
        $response->assertStatus(200)
            ->assertViewIs('enroll.thank-you');
    }

    /**
     * @test
     */
    public function valid_bought_with_valid_upsell_shows_up_correctly()
    {
        $courses = factory(Course::class, 2)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'upsell'    => 1
        ]);
        $text1 = "<first line of html>";
        $text2 = "CART_URL";
        $text3 = "<td>3rd line of html</td>";
        $text4 = "THANK_YOU_URL";

        factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $courseIS,
            'succeeds_course_id'     => $courses[1]->id,
            'html' => $text1 . $text2 . $text3 . $text4
        ]);

        $this->user->courses()->attach($courses[1]);

        $this->app['session']->put('recent_purchase_id', $courses[1]->id);

        $response = $this->get(route('enroll.after-sale', ['course' => $courses[1]]));
        $response->assertStatus(200)
                 ->assertViewIs('enroll.upsell')
                 ->assertSee($text1)
                 ->assertSee($text3)

                 ->assertDontSee($text2)
                 ->assertDontSee($text4)

                 ->assertSee(route('enroll', ['course' => $courses[1], 'token' => CourseUpsellToken::first()->token]))
                 ->assertSee(route('enroll.thank-you', ['course' => $courses[1]]));

        $this->assertDatabaseHas('course_upsell_tokens', [
            'used' => 0
        ]);
    }
}
