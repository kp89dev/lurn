<?php
namespace Unit\Service\VanillaJsConnect;

use App\Models\CourseVanillaForum;
use App\Models\Course;

class VanillaJsConnectUserTest extends \LoggedInTestCase
{

    public function testVanillaForumPrepRequestNoRules()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => null]);
        $this->user->courses()->attach($course, ['status' => 0]);

        $response = $this->post(route('webhook.vanilla.request', $course->id));
        $response->assertStatus(200)
            ->assertSee('{"showRules":false,"forum_link":"' . str_replace('/', '\/', $courseForum->url) . '"}');
    }

    public function testVanillaForumPrepRequestHasRules()
    {
        $course = factory(Course::class)->create();
        $vanilla = factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => '<p>rule</p>']);
        $this->user->courses()->attach($course, ['status' => 0]);

        $response = $this->post(route('webhook.vanilla.request', $course->id));
        $response->assertStatus(200)
            ->assertSee('{"showRules":true}');
    }

    public function testVanillaForumUpdateUserRulesStatus()
    {
        $course = factory(Course::class)->create();
        factory(CourseVanillaForum::class)->create(['course_id' => $course->id]);
        $this->user->courses()->attach($course, ['status' => 0]);

        $data = [
            'userId' => $this->user->id,
            'courseId' => $course->id,
            'status' => 1
        ];

        $response = $this->post(route('webhook.forum.rules'), $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('user_courses', [
            'user_id' => $this->user->id,
            'course_id' => $course->id,
            'forum_rules' => 1
        ]);
    }

    public function testVanillaForumHasRulesUserRedirect()
    {
        $course = factory(Course::class)->create();
        $vanilla = factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => '<p>Here are some rules</p>']);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $response = $this->get(route('webhook.vanilla'));
        $response->assertStatus(302)
            ->assertSee($course->id . '/forum');
    }

    public function testVanillaForumHasRulesUserAgreedRedirect()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => '<p>Here are some rules</p>']);
        $this->user->courses()->attach($course, ['forum_rules' => 1, 'status' => 0]);
        $this->app['session']->put('forum_c', $course->id);

        $response = $this->get(route('webhook.vanilla'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the v parameter.'
        ]);
    }

    public function testVanillaForumNoRulesUserRedirect()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => null]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $response = $this->get(route('webhook.vanilla'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the v parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingV()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $response = $this->json('GET', route('webhook.vanilla'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the v parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectWrongV()
    {
        $course = factory(Course::class)->create();
        factory(CourseVanillaForum::class)
            ->create([
                'course_id' => $course->id,
                'url' => 'lurn.nation.test',
                'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Unsupported version invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingClinetID()
    {
        $course = factory(Course::class)->create();
        factory(CourseVanillaForum::class)
            ->create([
                'course_id' => $course->id,
                'url' => 'lurn.nation.test',
                'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the client_id parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectWrongClinetID()
    {
        $course = factory(Course::class)->create();
        factory(CourseVanillaForum::class)
            ->create([
                'course_id' => $course->id,
                'url' => 'lurn.nation.test',
                'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_client',
                'message' => 'Unknown client invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingTimestamp()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'sig' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'The timestamp parameter is missing or invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectWrongTimestampFormat()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => 'invalid',
            'sig' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'The timestamp parameter is missing or invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectExpiredTimestamp()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->subMonth()->timestamp,
            'sig' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'The timestamp is invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingSig()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->subMonth()->timestamp,
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the sig parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingNonce()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->timestamp,
            'sig' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the nonce parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectMissingIP()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->timestamp,
            'sig' => 'invalid',
            'nonce' => 'invalid'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'invalid_request',
                'message' => 'Missing the ip parameter.'
        ]);
    }

    public function testVanillaForumSecureRedirectWrongSignature()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->timestamp,
            'sig' => 'invalid',
            'nonce' => 'invalid',
            'ip' => '127.0.0.1'
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'error' => 'access_denied',
                'message' => 'Signature invalid.'
        ]);
    }

    public function testVanillaForumSecureRedirectNoSigNoTimestamp()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
        ];

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
                'signedin' => true
        ]);
    }

    public function testVanillaForumSecureRedirectCorrectSignature()
    {
        $course = factory(Course::class)->create();
        $courseForum = factory(CourseVanillaForum::class)
            ->create([
            'course_id' => $course->id,
            'url' => 'lurn.nation.test',
            'forum_rules' => null
        ]);
        $this->user->courses()->attach($course);
        $this->app['session']->put('forum_c', $course->id);

        $data = [
            'v' => '2',
            'client_id' => $courseForum->client_id,
            'timestamp' => \Carbon\Carbon::now()->timestamp,
            'nonce' => 'valid',
            'ip' => '127.0.0.1'
        ];
        $data['sig'] = md5($data['ip'] . $data['nonce'] . $data['timestamp'] . $courseForum->client_secret);

        $response = $this->json('GET', route('webhook.vanilla'), $data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
                'ip' => '127.0.0.1',
        ]);
    }
}
