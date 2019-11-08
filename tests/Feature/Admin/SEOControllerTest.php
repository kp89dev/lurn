<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Course;

class SEOControllerTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function seoPageAvailable()
    {
        $this->get(route('seo.index'))
            ->assertSeeText('SEO Base Settings');
    }

    /**
     * @test
     */
    public function saveDefaultSettingsTest()
    {
        $response = $this->post(route('seo.update.default', [
            'title'     => 'Test',
            'site_name' => 'Test',
            'separator' => '-',
            'keywords'  => 'test',
        ]));

        $response->assertStatus(302)
                 ->assertRedirect(route('seo.index'));
    }

    /**
     * @test
     */
    public function saveCourseSettingsTest()
    {
        $course = factory(Course::class)->create();

        $this->get(route('courses.edit', ['course' => $course->id]));

        $response = $this->post(route('seo.update.course', [
            'course'      => $course->id,
            'title'       => 'Test',
            'site_name'   => 'Test',
            'separator'   => '-',
            'description' => 'Test description',
            'keywords'    => 'test',
        ]));

        $response->assertStatus(302)
                 ->assertRedirect(route('courses.edit', ['course' => $course->id]))
                 ->assertSessionHas('alert-success', 'Course SEO Settings Updated');
    }
}
