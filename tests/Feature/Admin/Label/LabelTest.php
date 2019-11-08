<?php

namespace Tests\Admin\Label;

use App\Models\Labels;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LabelTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function label_list_page_is_available()
    {
        $labels = factory(Labels::class, 5)->create();
        $response = $this->get(route('labels.index'));

        $response->assertSee('Labels')
            ->assertSeeText('Add New Label')
            ->assertStatus(200);

        $labels->each(function ($label) use ($response) {
            $response->assertSee($label->title);
        });
    }

    /**
     * @test
     */
    public function add_label_is_available()
    {
        $response = $this->get(route('labels.create'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_label()
    {
        $response = $this->post(
            route('labels.store'), [
                'title' => 'some label'
            ]
        );

        $this->assertDatabaseHas('labels', [
            'title'            => 'some label'
        ]);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function label_edit_page_is_available()
    {
        $label = factory(Labels::class)->create();

        $response = $this->get(route('labels.edit', [
                'label' => $label->id
        ]));

        $response->assertStatus(200);
        $response->assertSee($label->title);
    }

    /**
     * @test
     */
    public function successfully_edit_a_label()
    {
        $label = factory(Labels::class)->create();

        $response = $this->put(
            route('labels.update', ['label' => $label->id]), [
                'title'            => 'new title'
            ]
        );

        $this->assertDatabaseHas('labels', [
            'title'            => 'new title'
        ]);

        $response->assertRedirect(route('labels.index'))
            ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function label_gets_deleted()
    {
        $label = factory(Labels::class)->create();

        $this->delete(route('labels.destroy', $label->id));
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }
}
