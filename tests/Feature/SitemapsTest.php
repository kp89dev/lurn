<?php
namespace Tests\Feature;

use App\Models\NicheDetective\Niche;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\News;

class SitemapsTest extends \TestCase
{

    /**
     * @test
     */
    public function sitemap_index_is_available()
    {
        $response = $this->get(route('sitemap.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function sitemap_index_contains_correct_links()
    {
        $response = $this->get(route('sitemap.index'));
        $response->assertSee(route('sitemap.general'))
            ->assertSee(route('sitemap.legal'))
            ->assertSee(route('sitemap.career'))
            ->assertSee(route('sitemap.niches'))
            ->assertSee(route('sitemap.courses'))
            ->assertSee(route('sitemap.news'))
            ->assertSee('/blog/sitemap');
    }

    //dont work
    public function sitemap_is_xml_for_crawler()
    {
        $response = $this
            ->call('GET', route('sitemap.index'), [], [], ['HTTP_USER_AGENT' => 'crawl']);

        $response->assertStatus(200)
            ->assertHeader('content-type', 'text/xml');
    }

    /**
     * @test
     */
    public function site_map_general_available_and_correct()
    {
        $response = $this->get(route('sitemap.general'));

        $response->assertStatus(200)
            ->assertSee('/dashboard');
    }

    /**
     * @test
     */
    public function site_map_legal_available_and_correct()
    {
        $response = $this->get(route('sitemap.legal'));

        $response->assertStatus(200)
            ->assertSee('/privacy');
    }

    /**
     * @test
     */
    public function site_map_career_available()
    {
        $response = $this->get(route('sitemap.career'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function site_map_niche_available_and_correct()
    {
        $niche = factory(Niche::class)->create();

        $response = $this->get(route('sitemap.niches'));

        $response->assertStatus(200)
            ->assertSee('/tools/niche-detective/niche/' . $niche->id);
    }

    /**
     * @test
     */
    public function site_map_courses_available_and_correct()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);

        $response = $this->get(route('sitemap.courses'));

        $response->assertStatus(200)
            ->assertSee(route('course', ['course' => $course->slug]))
            ->assertSee(route('module', ['course' => $course->slug, 'module' => $module->slug]))
            ->assertSee(route('lesson', ['course' => $course->slug, 'module' => $module->slug, 'lesson' => $lesson->slug]));
    }

    /**
     * @test
     */
    public function assert_site_map_does_not_list_disabled_entities()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);
        $dcourse = factory(Course::class)->create(['status' => 0]);
        $dmodule = factory(Module::class)->create(['course_id' => $course->id, 'status' => 0]);
        $dlesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 0]);

        $response = $this->get(route('sitemap.courses'));

        $response->assertStatus(200)
            ->assertDontSee(route('course', ['course' => $dcourse->slug]))
            ->assertDontSee(route('module', ['course' => $course->slug, 'module' => $dmodule->slug]))
            ->assertDontSee(route('lesson', ['course' => $course->slug, 'module' => $module->slug, 'lesson' => $dlesson->slug]));
    }

    /**
     * @test
     */
    public function assert_news_stories_show_on_sitemap()
    {
        $news = factory(News::class)->create();

        $response = $this->get(route('sitemap.news'));

        $response->assertStatus(200)
            ->assertSee(route('news'))
            ->assertSee(route('news-article', ['news' => $news->slug]));
    }
}
