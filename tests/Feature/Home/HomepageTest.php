<?php
namespace Feature\Home;

use App\Models\Course;
use App\Models\CourseFeature;
use App\Models\StudentCount;
use App\Models\UserActivities;
use App\Models\UserSetting;
use Carbon\Carbon;
use TestCase;
use App\Models\User;

class HomepageTest extends TestCase
{
    /**
     * @test
     */
    public function homepage_is_available()
    {
        $this->get(route('home'))
             ->assertStatus(200);
    }

    /**
     * @test
     HIDDEN on homepage 
    public function featured_courses_are_shown()
    {
        $courses = factory(Course::class, 4)->create();

        foreach ($courses as $i => $course) {
            factory(CourseFeature::class)->create([
                'course_id' => $course->id,
                'free_bootcamp' => 0
            ]);

            StudentCount::create([
                'id'       => $course->id,
                'students' => $i * 100
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        foreach ($courses as $i => $course) {
            $response->assertSee(htmlspecialchars($course->title, ENT_QUOTES));
            $response->assertSee(htmlspecialchars($course->snippet, ENT_QUOTES));
            $response->assertSee('<i class="fa fa-user icon-circle-small"></i>');
        }
    }*/

    /**
     * @test
     HIDDEN on homepage 
    public function free_bootcamps_are_shown()
    {
        $courses = factory(Course::class, 3)->create();

        foreach ($courses as $i => $course) {
            factory(CourseFeature::class)->create([
                'course_id' => $course->id,
                'free_bootcamp' => 1
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        foreach ($courses as $i => $course) {
            $response->assertSee(htmlspecialchars($course->title, ENT_QUOTES));
            $response->assertSee(htmlspecialchars($course->snippet, ENT_QUOTES));
        }
    }*/

    /**
     * @test
     HIDDEN on homepage 
    public function user_acitivity_is_shown_if_image()
    {
        factory(UserActivities::class, 10)->create();
        $users = factory(User::class, 2)->create();
        factory(UserActivities::class)->create([
            'user_id'       => $users[0]->id,
            'activity_type' => 1,
            'activity_time' => (new Carbon('1 hour ago'))->toDateTimeString(),
            'activity_text' => 'Course finished'
        ]);

        factory(UserSetting::class)->create([
            'user_id' => $users[0]->id,
            'image' => str_slug('this is a test image')
        ]);

        factory(UserActivities::class)->create([
            'user_id'       => $users[1]->id,
            'activity_type' => 2,
            'activity_time' => (new Carbon('2 hours ago'))->toDateTimeString(),
            'activity_text' => 'Course bought'
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $response->assertSeeText($users[0]->name)
                 ->assertSeeText('1 hour ago')
                 ->assertSeeText('Course finished');

        $response->assertDontSeeText($users[1]->name);
    }*/
}



