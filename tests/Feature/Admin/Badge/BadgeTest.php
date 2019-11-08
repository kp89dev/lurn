<?php
namespace Feature\Admin\Badge;

use App\Models\Badge;
use App\Models\Course;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BadgeTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function badge_listing_is_available()
    {
        $course = factory(Course::class)->create();
        $badges = factory(Badge::class, 5)->create([
            'course_id' => $course->id
        ]);

        $response = $this->get(route('badges.index', compact('course')));

        $response->assertStatus(200)
                ->assertSeeText('Add Badge');

        foreach ($badges as $badge) {
            $response->assertSee($badge->image);
            $response->assertSee(htmlentities($badge->title));
        }
    }

    /**
     * @test
     */
    public function create_badge_page_is_available()
    {
        $course = factory(Course::class)->create();
        $response = $this->get(route('badges.create', compact('course')));

        $response->assertStatus(200)
                 ->assertSeeText('Badge Details');
    }

    /**
     * @test
     */
    public function edit_badge_page_is_available()
    {
        $course = factory(Course::class)->create();
        $badge = factory(Badge::class)->create(['course_id' => $course->id]);
        $response = $this->get(route('badges.edit', compact('course', 'badge')));

        $response->assertStatus(200)
            ->assertSeeText('Badge Details')
            ->assertSee(htmlentities($badge->title))
            ->assertSee($badge->content)
            ->assertSee($badge->image);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_badge()
    {
        Storage::fake('static');
        $course = factory(Course::class)->create();

        $badgeFields = [
            'title'   => 'some badge title',
            'content' => 'some content',
            'status'  => 1
        ];

        $response = $this->post(
            route('badges.store', compact('course')),
            $badgeFields + [
                'image' => UploadedFile::fake()->image('a_new_badge.png')
            ]
        );

        $response->assertRedirect(route('badges.index', compact('course')));
        
        $this->assertDatabaseHas('badges', $badgeFields + ['course_id' => $course->id]);

        Storage::disk('static')
                ->assertExists($course->badges()->first()->image);
    }

    /**
     * @test
     */
    public function successfully_edit_a_badge()
    {
        Storage::fake('static');
        $course = factory(Course::class)->create();
        $badge = factory(Badge::class)->create([
            'course_id' => $course->id,
            'image' => 'badges/'. $course->id . '/old_badge_image.png'
        ]);

        $badgeFields = [
            'title'   => 'some badge title',
            'content' => 'some content',
            'status'  => 1
        ];

        Storage::disk('static')
                ->putFileAs(
                    'badges/'. $course->id,
                    UploadedFile::fake()->image('an_old_image.png'),
                    'old_badge_image.png'
                );
        $response = $this->put(route('badges.update', compact('course', 'badge')), $badgeFields + [
                'image' => UploadedFile::fake()->image('a_new_badge.png')
            ]);

        $response->assertRedirect(route('badges.index', compact('course')));

        $this->assertDatabaseHas(
            'badges',
            $badgeFields + [
                'course_id' => $course->id,
                'id'    => $badge->id
            ]
        );

        Storage::disk('static')
                ->assertExists($course->badges()->first()->image);
        Storage::disk('static')
            ->assertMissing('badges/' .$course->id. '/old_badge_image.png');
    }

    /**
     * @test
     */
    public function destroy_removes_badge()
    {
        Storage::fake('static');
        $course = factory(Course::class)->create();
        $badge = factory(Badge::class)->create([
            'course_id' => $course->id,
            'image' => 'badges/'. $course->id . '/old_badge_image.png'
        ]);
        Storage::disk('static')
            ->putFileAs(
                'badges/'. $course->id,
                UploadedFile::fake()->image('an_old_image.png'),
                'old_badge_image.png'
            );
        $response = $this->delete(route('badges.destroy', compact('course', 'badge')));
        $response->assertRedirect(route('badges.index', compact('course')));
        Storage::disk('static')
            ->assertMissing('badges/' .$course->id. '/old_badge_image.png');
    }
}
