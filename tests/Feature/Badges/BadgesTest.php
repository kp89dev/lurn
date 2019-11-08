<?php
namespace App\Feature\Badges;

use App\Models\Badge;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\UserLoggedInTestCase;

class BadgesTest extends UserLoggedInTestCase
{
    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function badges_returns_404_when_course_has_no_badge()
    {
        $course = factory(Course::class)->create();
        $this->user->courses()->attach($course);
        
        $request = $this->get(route('front.badges.index', [
            'course' => $course->slug
        ]));

        $request->assertStatus(404);
    }

    /**
     * @test
     */
    public function badges_page_shows_enabled_badges_for_a_course()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $this->user->courses()->attach($course);

        $badges = factory(Badge::class, 2)->create([
            'course_id' => $course->id,
            'status'    => 1
        ]);

        $disabledBadges = factory(Badge::class, 2)->create([
            'course_id' => $course->id,
            'status'    => 0
        ]);

        $request = $this->get(route('front.badges.index', [
            'course' => $course->slug
        ]));

        $request->assertStatus(200);

        foreach ($badges as $badge) {
            $request->assertSee($badge->title)
                    ->assertSee($badge->content)
                    ->assertSee($badge->image);
        }

        foreach($disabledBadges as $b) {
            $request->assertDontSee($b->title)
                ->assertDontSee($b->content)
                ->assertDontSee($b->image);
        }
    }

    /**
     * @test
     */
    public function badge_request_page_is_available()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $this->user->courses()->attach($course);

        $badge = factory(Badge::class)->create([
            'course_id' => $course->id,
            'status'    => 1
        ]);

        $request = $this->get(route('front.badges.request', [
            'course' => $course->slug,
            'badge' => $badge->id
        ]));

        $request->assertStatus(200)
                ->assertSee($badge->title)
                ->assertSee($badge->content);
    }

    /**
     * @test
     */
    public function badge_request_page_saves_request_successfully()
    {
        Storage::fake('private');
        $course = factory(Course::class)->create(['status' => 1]);
        $this->user->courses()->attach($course);

        $badge = factory(Badge::class)->create([
            'course_id' => $course->id,
            'status'    => 1
        ]);

        $request = $this->post(route('front.badges.requestStore', [
            'course' => $course->slug,
            'badge' => $badge->id
        ]), [
            'comment' => 'some test comment',
            'proof' => [
                UploadedFile::fake()->image('a_proof.png'),
                UploadedFile::fake()->image('another_proof.png'),
            ]
        ]);

        $request->assertStatus(302)
                ->assertSessionHas('success');

        $this->assertDatabaseHas('badge_requests', [
            'badge_id' => $badge->id,
            'user_id'  => $this->user->id,
            'comment'  => 'some test comment',
        ]);

        $uploadedFiles = $this->user
                            ->badgeRequests()
                            ->where('badge_id', $badge->id)
                            ->first()
                            ->files()
                            ->get();

        Storage::disk('private')
            ->assertExists($uploadedFiles[0]->file_path);
        Storage::disk('private')
            ->assertExists($uploadedFiles[1]->file_path);
    }
}
