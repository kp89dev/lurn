<?php
namespace Tests\Admin;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\CourseContainer;
use App\Models\CourseSendlane;
use App\Models\DescriptionType;
use App\Models\Sendlane;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Sendlane\Sendlane as SendlaneService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Session;

class CourseTest extends \AdminLoggedInTestCase
{

    use DatabaseTransactions;

    /**
     * @test
     */
    public function course_page_is_available()
    {
        $response = $this->get(route('courses.index'));

        $response->assertSee('Courses')
            ->assertSee('Add New Course')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function courses_get_listed()
    {
        $courses = factory(Course::class, 10)->create([]);

        $response = $this->get(route('courses.index'));
        $response->assertStatus(200);

        foreach ($courses as $course) {
            $response->assertSee(htmlspecialchars($course->title, ENT_QUOTES))
                ->assertSee(htmlspecialchars($course->container->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function courses_get_listed_with_session()
    {
        $courses = factory(Course::class, 10)->create([]);

        Session::put('courses', $courses->pluck('id')->toArray());

        $response = $this->get(route('courses.index'));
        $response->assertStatus(200);

        foreach ($courses as $course) {
            $response->assertSee(htmlspecialchars($course->title, ENT_QUOTES))
                ->assertSee(htmlspecialchars($course->container->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function results_from_course_table_are_returned()
    {
        $courses = new Collection();
        for ($i = 0; $i < 6; $i++) {
            $courses[] = factory(Course::class)->create([
                'title' => str_random(5) . 'test'
            ]);
        }

        $response = $this->get(route('courses.search', ['term' => 'test']));

        $response->assertStatus(200);
        $response->assertJson($courses->jsonSerialize());

        foreach ($courses as $course) {
            if ($this->assertDatabaseHas('courses', ['title' => $course->title])) {
                continue;
            }

            self::assertTrue(
                false,
                "Failed when trying to see if the returned json contains course " . $course
            );
        }
    }

    /**
     * @test
     */
    public function add_course_page_is_available()
    {
        /** @var Course $course */
        $course = factory(Course::class)->create();

        $bonuses = factory(Course::class, 4)->create([]);

        foreach ($bonuses as $bonus) {
            $course->bonuses()->create(['course_id' => $course->id, 'bonus_course_id' => $bonus->id]);
        }

        $containers = factory(CourseContainer::class, 5)->create();

        $response = $this->get(route('courses.create'));
        $response->assertStatus(200);

        foreach ($containers as $container) {
            $response->assertSee(htmlspecialchars($container->title, ENT_QUOTES, 'UTF-8'));
        }
    }

    /**
     * @test
     */
    public function add_course_page_is_available_when_sendlane_account_is_picked()
    {
        $containers = factory(CourseContainer::class, 5)->create();
        $sendlane = factory(Sendlane::class)->create();
        $lists = [
            ['list_id' => 1, 'list_name' => 'test_list1'],
            ['list_id' => 2, 'list_name' => 'test_list2'],
            ['list_id' => 3, 'list_name' => 'test_list3'],
            ['list_id' => 4, 'list_name' => 'test_list4'],
            ['list_id' => 5, 'list_name' => 'test_list5']
        ];

        $sendlaneMock = \Mockery::mock(SendlaneService::class);
        $sendlaneMock->shouldReceive('request')->andReturn(new class($lists) {

            private $lists;

            public function __construct($lists)
            {
                $this->lists = $lists;
            }

            public function getBody()
            {
                return json_encode($this->lists);
            }
        });

        $this->app->bind(SendlaneService::class, function ($app) use ($sendlaneMock) {
            return $sendlaneMock;
        });

        $response = $this->get(route('courses.create') . '?sendlane=' . $sendlane->id);
        $response->assertStatus(200);

        foreach ($containers as $container) {
            $response->assertSee(htmlentities($container->title, ENT_QUOTES));
        }

        foreach ($lists as $list) {
            $response->assertSee($list['list_id'] . '|' . $list['list_name']);
            $response->assertSee('>' . $list['list_name'] . '<');
        }
    }

    /**
     * @test
     */
    public function successfully_add_a_new_course()
    {
        $container = factory(CourseContainer::class)->create();

        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }

        $postRegistrationDescription = 'test post registration description';
        $descriptionType = DescriptionType::whereName('post-registration')->first();

        $bunusCourseTitle = 'Test Bonus Course';
        $bonus = factory(Course::class)->create(['title' => $bunusCourseTitle]);

        $response = $this->post(
            route('courses.store'),
            [
                'title' => 'some title',
                'description' => 'some kind of description',
                'post-registration-description' => $postRegistrationDescription,
                'snippet' => 'some sort of snippet',
                'status' => 1,
                'purchasable' => 1,
                'course_container_id' => $container->id,
                'is_product_id' => 100,
                'is_subscription_product_id' => 100,
                'is_account' => 'JP126',
                'price' => 1497,
                'subscription' => 1,
                'sendlaneAccount' => 10,
                'sendlaneList' => '78|testing_list',
                'client_id' => '12345',
                'client_secret' => 'secret',
                'url' => 'http://google.com',
                'confirm_after' => 'M',
                'bonus_of' => $bonus->id,
            ]
        );

        $this->assertDatabaseHas('courses', [
            'title' => 'some title',
            'description' => 'some kind of description',
            'snippet' => 'some sort of snippet',
            'status' => 1,
            'purchasable' => 1,
            'course_container_id' => $container->id,
            'confirm_after' => 'M',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'price' => 1497,
            'subscription' => 1,
            'is_product_id' => 100,
            'is_subscription_product_id' => 100,
            'is_account' => 'JP126'
        ]);

        $this->assertDatabaseHas('course_sendlane', [
            'course_id' => Course::where('title', '!=', $bunusCourseTitle)->first()->id,
            'sendlane_id' => 10,
            'list_id' => 78,
            'list_name' => 'testing_list'
        ]);

        $this->assertDatabaseHas('custom_descriptions', [
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescription,
        ]);

        $response->assertRedirect(route('courses.index'))
            ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function edit_page_can_be_accessed()
    {
        /** @var Course $course */
        $course = factory(Course::class)->create();

        $bonuses = factory(Course::class, 4)->create([]);

        $existingCourse = factory(Course::class)->create(['status' => 1]);

        foreach ($bonuses as $bonus) {
            $course->bonuses()->create(['course_id' => $course->id, 'bonus_course_id' => $bonus->id]);
        }

        $bonus = factory(Course::class)->create();
        factory(CourseBonus::class)->create(['course_id' => $existingCourse->id, 'bonus_course_id' => $bonus->id]);

        $response = $this->get(route('courses.edit', ['course' => $existingCourse->id]));

        $response->assertStatus(200)
            ->assertSee($existingCourse->title)
            ->assertSee($existingCourse->description);
    }

    /**
     * @test
     */
    public function edit_page_can_be_accessed_when_sendlane_id_is_picked()
    {
        $existingCourse = factory(Course::class)->create(['status' => 1]);
        $sendlane = factory(Sendlane::class)->create();

        $lists = [
            ['list_id' => 1, 'list_name' => 'test_list1'],
            ['list_id' => 2, 'list_name' => 'test_list2'],
            ['list_id' => 3, 'list_name' => 'test_list3'],
            ['list_id' => 4, 'list_name' => 'test_list4'],
            ['list_id' => 5, 'list_name' => 'test_list5']
        ];

        $sendlaneMock = \Mockery::mock(SendlaneService::class);
        $sendlaneMock->shouldReceive('request')->andReturn(new class($lists) {

            private $lists;

            public function __construct($lists)
            {
                $this->lists = $lists;
            }

            public function getBody()
            {
                return json_encode($this->lists);
            }
        });

        $this->app->bind(SendlaneService::class, function ($app) use ($sendlaneMock) {
            return $sendlaneMock;
        });

        $response = $this->get(route('courses.edit', ['course' => $existingCourse->id]) . '?sendlane=' . $sendlane->id);

        $response->assertStatus(200)
            ->assertSee($existingCourse->title)
            ->assertSee($existingCourse->description);

        foreach ($lists as $list) {
            $response->assertSee($list['list_id'] . '|' . $list['list_name']);
            $response->assertSee('>' . $list['list_name'] . '<');
        }
    }

    /**
     * @test
     */
    public function successfully_edit_a_course()
    {
        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }

        $postRegistrationDescriptionA = 'test post registration description A';
        $postRegistrationDescriptionB = 'test post registration description B';
        $descriptionType = DescriptionType::whereName('post-registration')->first();

        /** @var Course $prexistingCourse */
        $prexistingCourse = factory(Course::class)->create(['status' => 1]);

        /** @var Course $existingCourse */
        $existingCourse = factory(Course::class)->create(['status' => 1]);
        $existingCourse->customDescriptions()->create([
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionA,
        ]);

        $newContainer = factory(CourseContainer::class)->create();
        factory(CourseSendlane::class)->create([
            'course_id' => $existingCourse->id
        ]);

        $bunusCourseTitle = 'Test Bonus Course';
        $bonus = factory(Course::class)->create(['title' => $bunusCourseTitle]);

        $response = $this->put(
            route('courses.update', [$existingCourse->id]),
            [
                'title' => 'new title',
                'description' => 'new description',
                'post-registration-description' => $postRegistrationDescriptionB,
                'snippet' => 'some sort of snippet',
                'status' => 0,
                'course_container_id' => $newContainer->id,
                'is_product_id' => 200,
                'is_subscription_product_id' => 200,
                'is_account' => 'JP126',
                'price' => 1500,
                'subscription' => 1,
                'sendlaneAccount' => 10,
                'sendlaneList' => '78|testing_list',
                'client_id' => '12345',
                'client_secret' => 'secret',
                'url' => 'http://google.com',
                'recommended1' => $prexistingCourse->id,
                'recommended2' => 'none',
                'recommended3' => 'none',
                'recommended4' => 'none',
                'confirm_after' => 'M',
                'bonus_of' => $bonus->id,
            ]
        );

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'new title',
            'description' => 'new description',
            'status' => 0,
            'purchasable' => 0,
            'course_container_id' => $newContainer->id,
            'confirm_after' => 'M',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'course_id' => $existingCourse->id,
            'price' => 1500,
            'subscription' => 1,
            'is_product_id' => 200,
            'is_subscription_product_id' => 200,
            'is_account' => 'JP126'
        ]);

        $this->assertDatabaseHas('course_sendlane', [
            'course_id' => $existingCourse->id,
            'sendlane_id' => 10,
            'list_id' => 78,
            'list_name' => 'testing_list'
        ]);

        $this->assertDatabaseHas('custom_descriptions', [
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionB,
        ]);

        $response->assertRedirect(route('courses.index'))
            ->assertSessionMissing('errors');

        $this->assertCount(1, CourseSendlane::all());
    }

    /**
     * @test
     */
    public function successfully_edit_a_course_remove_bonus()
    {
        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }

        $postRegistrationDescriptionA = 'test post registration description A';
        $postRegistrationDescriptionB = 'test post registration description B';
        $descriptionType = DescriptionType::whereName('post-registration')->first();

        /** @var Course $prexistingCourse */
        $prexistingCourse = factory(Course::class)->create(['status' => 1]);

        /** @var Course $existingCourse */
        $existingCourse = factory(Course::class)->create(['status' => 1]);
        $existingCourse->customDescriptions()->create([
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionA,
        ]);

        $newContainer = factory(CourseContainer::class)->create();
        factory(CourseSendlane::class)->create([
            'course_id' => $existingCourse->id
        ]);

        $bonus = factory(Course::class)->create();
        factory(CourseBonus::class)->create([
            'course_id' => $bonus->id,
            'bonus_course_id' => $existingCourse->id
        ]);

        $response = $this->put(
            route('courses.update', [$existingCourse->id]),
            [
                'title' => 'new title',
                'description' => 'new description',
                'post-registration-description' => $postRegistrationDescriptionB,
                'snippet' => 'some sort of snippet',
                'status' => 0,
                'course_container_id' => $newContainer->id,
                'is_product_id' => 200,
                'is_subscription_product_id' => 200,
                'is_account' => 'JP126',
                'price' => 1500,
                'subscription' => 1,
                'sendlaneAccount' => 10,
                'sendlaneList' => '78|testing_list',
                'client_id' => '12345',
                'client_secret' => 'secret',
                'url' => 'http://google.com',
                'recommended1' => $prexistingCourse->id,
                'recommended2' => 'none',
                'recommended3' => 'none',
                'recommended4' => 'none',
                'confirm_after' => 'M',
                'bonus_of' => 'none',
            ]
        );

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'new title',
            'description' => 'new description',
            'status' => 0,
            'course_container_id' => $newContainer->id,
            'confirm_after' => 'M',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'course_id' => $existingCourse->id,
            'price' => 1500,
            'subscription' => 1,
            'is_product_id' => 200,
            'is_subscription_product_id' => 200,
            'is_account' => 'JP126'
        ]);

        $this->assertDatabaseHas('course_sendlane', [
            'course_id' => $existingCourse->id,
            'sendlane_id' => 10,
            'list_id' => 78,
            'list_name' => 'testing_list'
        ]);

        $this->assertDatabaseHas('custom_descriptions', [
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionB,
        ]);

        $response->assertRedirect(route('courses.index'))
            ->assertSessionMissing('errors');

        $this->assertCount(1, CourseSendlane::all());
    }

    /**
     * @test
     */
    public function successfully_edit_a_course_check_for_existing_bonus()
    {
        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }

        $postRegistrationDescriptionA = 'test post registration description A';
        $postRegistrationDescriptionB = 'test post registration description B';
        $descriptionType = DescriptionType::whereName('post-registration')->first();

        /** @var Course $prexistingCourse */
        $prexistingCourse = factory(Course::class)->create(['status' => 1]);

        /** @var Course $existingCourse */
        $existingCourse = factory(Course::class)->create(['status' => 1]);
        $existingCourse->customDescriptions()->create([
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionA,
        ]);

        $newContainer = factory(CourseContainer::class)->create();
        factory(CourseSendlane::class)->create([
            'course_id' => $existingCourse->id
        ]);

        $bonus = factory(Course::class)->create();
        factory(CourseBonus::class)->create([
            'course_id' => $bonus->id,
            'bonus_course_id' => $existingCourse->id
        ]);

        $response = $this->put(
            route('courses.update', [$existingCourse->id]),
            [
                'title' => 'new title',
                'description' => 'new description',
                'post-registration-description' => $postRegistrationDescriptionB,
                'snippet' => 'some sort of snippet',
                'status' => 0,
                'course_container_id' => $newContainer->id,
                'is_product_id' => 200,
                'is_subscription_product_id' => 200,
                'is_account' => 'JP126',
                'price' => 1500,
                'subscription' => 1,
                'sendlaneAccount' => 10,
                'sendlaneList' => '78|testing_list',
                'client_id' => '12345',
                'client_secret' => 'secret',
                'url' => 'http://google.com',
                'recommended1' => $prexistingCourse->id,
                'recommended2' => 'none',
                'recommended3' => 'none',
                'recommended4' => 'none',
                'confirm_after' => 'M',
                'bonus_of' => $bonus->id,
            ]
        );

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'new title',
            'description' => 'new description',
            'status' => 0,
            'course_container_id' => $newContainer->id,
            'confirm_after' => 'M',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'course_id' => $existingCourse->id,
            'price' => 1500,
            'subscription' => 1,
            'is_product_id' => 200,
            'is_subscription_product_id' => 200,
            'is_account' => 'JP126'
        ]);

        $this->assertDatabaseHas('course_sendlane', [
            'course_id' => $existingCourse->id,
            'sendlane_id' => 10,
            'list_id' => 78,
            'list_name' => 'testing_list'
        ]);

        $this->assertDatabaseHas('custom_descriptions', [
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionB,
        ]);

        $response->assertRedirect(route('courses.index'))
            ->assertSessionMissing('errors');

        $this->assertCount(1, CourseSendlane::all());
    }

    /**
     * @test
     */
    public function successfully_edit_a_course_update_bonus()
    {
        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }

        $postRegistrationDescriptionA = 'test post registration description A';
        $postRegistrationDescriptionB = 'test post registration description B';
        $descriptionType = DescriptionType::whereName('post-registration')->first();

        /** @var Course $prexistingCourse */
        $prexistingCourse = factory(Course::class)->create(['status' => 1]);

        /** @var Course $existingCourse */
        $existingCourse = factory(Course::class)->create(['status' => 1]);
        $existingCourse->customDescriptions()->create([
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionA,
        ]);

        $newContainer = factory(CourseContainer::class)->create();
        factory(CourseSendlane::class)->create([
            'course_id' => $existingCourse->id
        ]);

        $bonus = factory(Course::class)->create();
        $bonusCourse = factory(Course::class)->create();
        factory(CourseBonus::class)->create([
            'course_id' => $bonusCourse->id,
            'bonus_course_id' => $existingCourse->id
        ]);

        $response = $this->put(
            route('courses.update', [$existingCourse->id]),
            [
                'title' => 'new title',
                'description' => 'new description',
                'post-registration-description' => $postRegistrationDescriptionB,
                'snippet' => 'some sort of snippet',
                'status' => 0,
                'course_container_id' => $newContainer->id,
                'is_product_id' => 200,
                'is_subscription_product_id' => 200,
                'is_account' => 'JP126',
                'price' => 1500,
                'subscription' => 1,
                'sendlaneAccount' => 10,
                'sendlaneList' => '78|testing_list',
                'client_id' => '12345',
                'client_secret' => 'secret',
                'url' => 'http://google.com',
                'recommended1' => $prexistingCourse->id,
                'recommended2' => 'none',
                'recommended3' => 'none',
                'recommended4' => 'none',
                'confirm_after' => 'M',
                'bonus_of' => $bonus->id,
            ]
        );

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'new title',
            'description' => 'new description',
            'status' => 0,
            'course_container_id' => $newContainer->id,
            'confirm_after' => 'M',
        ]);

        $this->assertDatabaseHas('course_infusionsoft', [
            'course_id' => $existingCourse->id,
            'price' => 1500,
            'subscription' => 1,
            'is_product_id' => 200,
            'is_subscription_product_id' => 200,
            'is_account' => 'JP126'
        ]);

        $this->assertDatabaseHas('course_sendlane', [
            'course_id' => $existingCourse->id,
            'sendlane_id' => 10,
            'list_id' => 78,
            'list_name' => 'testing_list'
        ]);

        $this->assertDatabaseHas('custom_descriptions', [
            'description_type_id' => $descriptionType->id,
            'description' => $postRegistrationDescriptionB,
        ]);

        $response->assertRedirect(route('courses.index'))
            ->assertSessionMissing('errors');

        $this->assertCount(1, CourseSendlane::all());
    }

    /**
     * @test
     */
    public function course_bonuses_request_lists_correctly()
    {
        /** @var Course $course */
        $course = factory(Course::class)->create();

        $bonuses = factory(Course::class, 4)->create([]);

        foreach ($bonuses as $bonus) {
            $course->bonuses()->create(['course_id' => $course->id, 'bonus_course_id' => $bonus->id]);
        }

        $response = $this->call('GET', route('courses.index'), ['course' => $course->id, 'bonuses' => true]);

        foreach ($bonuses as $b) {
            $response->assertSee(htmlspecialchars($b->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function course_category_request_lists_correctly()
    {
        $courses = factory(Course::class, 6)->create();

        $category = factory(Category::class)->create();

        /** @var Course $course */
        foreach ($courses as $course) {
            $course->categories()->attach($category->id);
        }

        $response = $this->call('GET', route('courses.index'), ['course' => $course->id, 'category' => $category->id]);

        foreach ($courses as $c) {
            $response->assertSee(htmlspecialchars($c->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function thumbnail_is_saved()
    {
        $course = factory(Course::class)->create();

        $response = $this->put(route('courses.update', $course), [
            'thumbnail'       => UploadedFile::fake()->image('test.jpg'),
            'title'           => 'new title',
            'description'     => 'new description',
            'snippet'         => 'some sort of snippet',
            'status'          => 1,
            'is_product_id'   => 200,
            'is_subscription_product_id' => 0,
            'is_account'      => 'JP126',
            'price'           => 1500,
            'subscription'    => 1,
            'sendlaneAccount' => 10,
            'client_id'       => '12345',
            'client_secret'   => 'secret',
            'url'             => 'http://google.com',
            'recommended1'    => 'none',
            'recommended2'    => 'none',
            'recommended3'    => 'none',
            'recommended4'    => 'none',
            'confirm_after'   => 'M',
        ]);

        $sessionHasErrors = app('session.store')->has('errors');
        self::assertFalse(
            $sessionHasErrors,
            "Error: " . ($sessionHasErrors ? app('session.store')->get('errors')->first() : "")
        );

        $response->assertRedirect(route('courses.index'))
                 ->assertDontSee($course->getPrintableImageUrl());

        self::assertTrue(strpos($course->getPrintableImageUrl(), 'images/default-course-thumbnail.png') === false);
    }
    
    /**
     * @test
     */
    public function edit_course_purchasability_direct()
    {
        $course = factory(Course::class)->create();

        $response = $this->put(route('courses.update', $course), [
            'title'           => 'new title',
            'description'     => 'new description',
            'snippet'         => 'some sort of snippet',
            'status'          => 1,
            'purchasable'     => 0,
            'is_product_id'   => 200,
            'is_subscription_product_id' => 0,
            'is_account'      => 'JP126',
            'price'           => 1500,
            'subscription'    => 1,
            'sendlaneAccount' => 10,
            'client_id'       => '12345',
            'client_secret'   => 'secret',
            'url'             => 'http://google.com',
            'recommended1'    => 'none',
            'recommended2'    => 'none',
            'recommended3'    => 'none',
            'recommended4'    => 'none',
            'confirm_after'   => 'M',
        ]);

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));
        
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'status' => 1,
            'purchasable' => 0,
        ]);
    }
    
    /**
     * @test
     */
    public function edit_course_purchasability_indirect()
    {
        $course = factory(Course::class)->create();

        $response = $this->put(route('courses.update', $course), [
            'title'           => 'new title',
            'description'     => 'new description',
            'snippet'         => 'some sort of snippet',
            'status'          => 0,
            'is_product_id'   => 200,
            'is_subscription_product_id' => 0,
            'is_account'      => 'JP126',
            'price'           => 1500,
            'subscription'    => 1,
            'sendlaneAccount' => 10,
            'client_id'       => '12345',
            'client_secret'   => 'secret',
            'url'             => 'http://google.com',
            'recommended1'    => 'none',
            'recommended2'    => 'none',
            'recommended3'    => 'none',
            'recommended4'    => 'none',
            'confirm_after'   => 'M',
        ]);

        $response->assertSessionMissing('errors')
            ->assertRedirect(route('courses.index'));
        
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'status' => 0,
            'purchasable' => 0,
        ]);
    }
    
}
