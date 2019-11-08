<?php
namespace Tests\Feature\Admin\User;

use App\Models\Badge;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\User;
use App\Services\Woopra\Woopra;
use Illuminate\Support\Facades\DB;

class UserProfileTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function viewing_profile_shows_activity_corectly()
    {
        $user = factory(User::class)->create();
        $woopraMock = $this->createMock(Woopra::class);
        $woopraMock->expects(self::once())
                   ->method('post')
                   ->with('profile/visits')
                    ->willReturn(new class() {
                        public function getBody()
                        {
                            return json_encode([
                                'visits' => [[
                                    'region' => 'Region1',
                                    'city'   => 'City1',
                                    'date'   => 'test date #1',
                                    'country' => 'ro',
                                    'actions' => [[
                                        'date' => 'test date #2',
                                        'icon'  => 'test.jpb',
                                        'name' => '',
                                        'description' => 'some desc'
                                    ]]
                                ]]
                            ]);
                        }
                    });

        $this->app->bind(Woopra::class, function ($app) use ($woopraMock) {
            return $woopraMock;
        });

        $response = $this->get(route('users.show', ['id' => $user->id]));
        //$response =  $this->get(route('user-logins.index'));
        $response->assertStatus(200)
                 ->assertSeeText('Region1')
                 ->assertSeeText('City')
                 ->assertSeeText('some desc');

    }

    /**
     * @test
     */
    public function viewing_profile_shows_purchases_corectly()
    {
        $courses    = factory(Course::class, 2)->create();
        $user       = factory(User::class)->create();

        factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'price'   => 2222
        ]);

        factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[1]->id,
            'price'     => 2223
        ]);
        $user->courses()->sync([
            $courses[0]->id,
            $courses[1]->id => ['cancelled_at' => now()]
        ]);
        $woopraMock = $this->createMock(Woopra::class);
        $woopraMock->expects(self::once())
            ->method('post')
            ->with('profile/visits')
            ->willReturn(new class() {
                public function getBody()
                {
                    return json_encode([
                        'visits' => []
                    ]);
                }
            });

        $this->app->bind(Woopra::class, function ($app) use ($woopraMock) {
            return $woopraMock;
        });

        $response = $this->get(route('users.show', ['id' => $user->id]));

        $response->assertStatus(200);
        foreach ($courses as $course) {
            $response->assertSee($course->title);
        }

        $response->assertSeeText(number_format(2222, 0))
                 ->assertSeeText(number_format(2223, 0));
    }

    /**
     * @test
     */
    public function viewing_profile_shows_badges_correctly()
    {
        $badges = factory(Badge::class, 3)->create(['status' => 1]);
        $user   = factory(User::class)->create();
        DB::table('user_badges')->insert([
            ['user_id' => $user->id, 'badge_id' => $badges[0]->id],
            ['user_id' => $user->id, 'badge_id' => $badges[1]->id],
        ]);

        $woopraMock = $this->createMock(Woopra::class);
        $woopraMock->expects(self::once())
            ->method('post')
            ->with('profile/visits')
            ->willReturn(new class() {
                public function getBody()
                {
                    return json_encode([
                        'visits' => []
                    ]);
                }
            });

        $this->app->bind(Woopra::class, function ($app) use ($woopraMock) {
            return $woopraMock;
        });

        $response = $this->get(route('users.show', ['id' => $user->id]));
        $response->assertStatus(200);

        foreach ($badges->take(2) as $badge) {
            $response->assertSee($badge->src)
                    ->assertSee($badge->title);
        }

        $response->assertDontSee($badges[2]->src)
                 ->assertDontSee($badges[2]->title);
    }
}
