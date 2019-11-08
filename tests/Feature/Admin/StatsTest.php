<?php

namespace Tests\Admin;

use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserLogin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class StatsTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function stats_page_is_available()
    {
        $response = $this->get(route('stats.index'));

        $response->assertSee('Total Daily User Revenue Reporting')
            ->assertSee('Report')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_stats_get_listed()
    {
        $users = factory(User::class, 10)->create(['created_at' => Carbon::now()->addDay(15)]);

        $response = $this->post(route('stats.detailed'), ['type' => 'user', 'days' => 10]);
        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee(htmlentities($user->name, ENT_QUOTES));
        }

        $response = $this->post(route('stats.detailed'), ['type' => 'countries', 'days' => 10]);
        $response->assertStatus(200);

        foreach ($users as $user) {
            /** @var User $user */
            if ($user->getCountry()) {
                $response->assertSee(htmlentities($user->getCountry(), ENT_QUOTES));
            }
        }
    }

    /**
     * @test
     */
    public function average_calculations_check()
    {

        $users = factory(User::class, 3)->create([
            'status' => 1,
            'created_at' => Carbon::now()->subDays(31)
        ]);

        $infusionCourses = [];
        $courses = factory(Course::class, 3)->create();
        foreach ($courses as $course) {
            $infusionCourses[] = factory(CourseInfusionsoft::class)->create(['course_id' => $course->id, ]);
        }
        $totals = 0;
        $userCourses = [];
        foreach ($users as $user) {
            foreach ($infusionCourses as $infusionCourse) {
                $userCourses[] = factory(UserCourse::class)
                    ->create([
                        'user_id' => $user->id,
                        'payments_made' => 1,
                        'course_infusionsoft_id' => $infusionCourse->id,
                        'course_id' => $infusionCourse->course_id,
                        'created_at' => Carbon::now()->subDays(10)
                    ]);
                $totals+= $infusionCourse->price;
            }
        }

        $response = $this->post(route('stats.average'), ['days' => 30]);
        $response->assertSee(number_format($totals / count($users)), 2);
    }

}