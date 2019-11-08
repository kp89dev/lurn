<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\NicheDetective\Category;
use App\Models\NicheDetective\Niche;
use App\Models\CourseTool;
use App\Models\Course;

class NicheTest extends \LoggedInTestCase
{
    public function setUp()
    {
        parent::setUp();
        $course = factory(Course::class)->create();
        $courseTool = factory(CourseTool::class)->create([
            'course_id' => $course->id,
            'tool_name' => 'Niche Detective'
        ]);

        $this->user->enroll($course);
    }

    /**
     * @test
     */
    public function niche_index_shows_niches()
    {
        $cats = Factory(Category::class, 5)->create();
        $response = $this->actingAs($this->user)
            ->get(route('niche-tool'));

        $response->assertStatus(200);
        foreach($cats as $c) {
            $response->assertSee(htmlspecialchars($c->label, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function niche_ids_return_correctly()
    {
        $category = factory(Category::class)->create();
        $niches = factory(Niche::class, 5)->create(['category_id' => $category->id]);

        $response = $this->post(route('get-niche-categories', ['id' => $category->id]));

        $json = $response->getContent();

        $jsonObj = json_decode($json);

        $this->assertObjectHasAttribute('status', $jsonObj);
        $this->assertObjectHasAttribute('niches', $jsonObj);

        foreach($niches as $niche) {
            $this->assertContains((string)$niche->id, $json);
            $this->assertContains((string)$niche->label, $json);
        }
    }

    /**
     * @test
     */
    public function json_error_when_missing_niches()
    {
        $category = factory(Category::class)->create();

        $response = $this->post(route('get-niche-categories', ['id' => $category->id]));

        $json = $response->getContent();

        $jsonObj = json_decode($json);

        $this->assertTrue($jsonObj->status === '1');
        $this->assertTrue($jsonObj->msg == 'error');

    }

    /**
     * @test
     */
    public function niche_report_available()
    {
        $category = factory(Category::class)->create();
        $niche = factory(Niche::class)->create([
            'category_id' => $category->id,
            'top_keywords'      => '["a", "b", "c"]',
            'location'          => '[{"country":"United States","percentage":""},{"country":"India","percentage":""}]',
            'hot_products'      => '[{"name":"Prod","benefit1":"B1","benefit2":"B2","benefit3":"B3",
                                    "url":"http://lurn.com","affiliateMarketPlace":"NotReal"}]',
        ]);

        $response = $this->get(route('niche-detail', ['id' => $niche->id]));

        $response->assertStatus(200)
            ->assertSee($niche->label);
    }
}
