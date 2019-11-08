<?php
namespace App\Feature\Admin\Badge;

use App\Models\Badge;
use App\Models\Badge\BadgeRequest;
use App\Models\Course;
use App\Models\User;

class BadgeRequestTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function request_page_is_returning_unapproved_badge_requests()
    {
        $course = factory(Course::class)->create();
        $badges = factory(Badge::class, 2)->create(['course_id' => $course->id]);
        $user = factory(User::class)->create();

        $badgeReq = factory(BadgeRequest::class, 2)->create([
            'status'   => 0,
            'badge_id' => $badges[0]->id,
            'user_id'  => $user->id
        ]);

        $badgeReqApproved = factory(BadgeRequest::class)->create([
            'status' => 1,
            'badge_id' => $badges[1]->id,
            'user_id' => $user->id
        ]);
        $badgeReqRejected = factory(BadgeRequest::class)->create([
            'status' => 2,
            'badge_id' => $badges[0]->id,
            'user_id' => $user->id
        ]);

        $request = $this->get(route('badge.requests.new'));

        $request->assertStatus(200);

        foreach($badgeReq as $brq) {
            $request->assertSee($brq->comment)
                    ->assertSee($user->name);
        }

        $request->assertDontSee($badgeReqApproved->comment);
        $request->assertDontSee($badgeReqRejected->comment);
    }

    /**
     * @test
     */
    public function request_page_is_returning_rejected_approved_badge_requests()
    {
        $course = factory(Course::class)->create();
        $badges = factory(Badge::class, 2)->create(['course_id' => $course->id]);
        $user = factory(User::class)->create();

        $badgeReq = factory(BadgeRequest::class, 2)->create([
            'status'   => 0,
            'badge_id' => $badges[0]->id,
            'user_id'  => $user->id
        ]);

        $badgeReqApproved = factory(BadgeRequest::class)->create([
            'status' => 1,
            'badge_id' => $badges[1]->id,
            'user_id' => $user->id
        ]);
        $badgeReqRejected = factory(BadgeRequest::class)->create([
            'status' => 2,
            'badge_id' => $badges[0]->id,
            'user_id' => $user->id
        ]);

        $request = $this->get(route('badge.requests.old'));

        $request->assertStatus(200);

        foreach($badgeReq as $brq) {
            $request->assertDontSeeText(htmlentities($brq->comment, ENT_QUOTES));
        }

        $request->assertSee(htmlentities($badgeReqApproved->comment, ENT_QUOTES));
        $request->assertSee(htmlentities($badgeReqRejected->comment, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function request_is_approved_successfully()
    {
        $user = factory(User::class)->create(['email' => 'nroevfkt@sharklasers.com']);
        $badge = factory(Badge::class)->create(['credly_id' => 119097]);
        $badgeReq = factory(BadgeRequest::class)->create([
            'status'   => 0,
            'user_id'  => $user->id,
            'badge_id' => $badge->id
        ]);

        $request = $this->post(route('badge.requests.approve', ['badge' => $badgeReq]));

        $request->assertStatus(302)
                ->assertSessionHas('success');

        $this->assertDatabaseHas('badge_requests', [
            'user_id' => $user->id,
            'badge_id' => $badgeReq->badge_id,
            'status' => 1
        ]);
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $user->id,
            'badge_id' => $badgeReq->badge_id
        ]);
    }

    /**
     * @test
     */
    public function request_is_rejected()
    {
        $user = factory(User::class)->create();
        $badgeReq = factory(BadgeRequest::class)->create([
            'status'   => 0,
            'user_id'  => $user->id
        ]);

        $request = $this->post(route('badge.requests.reject', ['badge' => $badgeReq]));

        $request->assertStatus(302)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('badge_requests', [
            'user_id' => $user->id,
            'badge_id' => $badgeReq->badge_id,
            'status' => 2
        ]);
        $this->assertDatabaseMissing('user_badges', [
            'user_id' => $user->id,
            'badge_id' => $badgeReq->badge_id
        ]);
    }
}
