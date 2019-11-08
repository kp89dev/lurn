<?php

namespace Feature\Admin;

use App\Models\Template;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplateTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function templates_are_listed()
    {
        $templates = Factory(Template::class, 3)->create();

        $response = $this->get(route('templates.index'));

        $response->assertStatus(200)
            ->assertSee("Add New Template");

        foreach($templates as $t) {
            $response->assertSee(htmlspecialchars($t->title, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function template_create_page_available()
    {
        $response = $this->get(route('templates.create'));

        $response->assertStatus(200)
            ->assertSee('$$USERNAME$$');
    }

    /**
     * @test
     */
    public function template_edit_page_available()
    {
        $template = Factory(Template::class)->create();

        $response = $this->get(route('templates.edit', $template));

        $response->assertStatus(200)
            ->assertSee('$$USERNAME$$')
            ->assertSee(htmlspecialchars($template->title, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function template_can_be_created()
    {
        $template = factory(Template::class)->create();

        $this->post(route('templates.store'), $template = [
            'title' => $template->title,
            'content' => $template->content,
            'subject' => $template->subject,
            'user_id' => $template->user_id
        ]);

        $this->assertDatabaseHas('templates', $template);
    }

    /**
     * @test
     */
    public function template_can_be_edidited()
    {
        $template = factory(Template::class)->create();
        $newStuff = factory(Template::class)->create();

        $response = $this->put(route('templates.update', $template->id), [
            'title' => $newStuff->title,
            'content' => $newStuff->content
        ]);

        $this->assertDatabaseHas('templates', [
            'id' => $template->id,
            'title' => $newStuff->title,
            'content' => $newStuff->content
        ]);
    }

    /**
     * @test
     */
    public function template_preview_is_available()
    {
        $response = $this->post(route('templates.preview'), ['content' => "<p>This is a test!</p>"]);

        $response->assertStatus(200)
                 ->assertSee('This is a test!');
    }
}
