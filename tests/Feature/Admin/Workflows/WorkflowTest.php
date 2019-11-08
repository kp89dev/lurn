<?php

namespace Tests\Feature\Admin\Workflows;

use Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Workflows\Workflow;
use App\Services\Workflows\View\ActionCollection;
use App\Services\Workflows\View\ConditionCollection;

class WorkflowTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function workflow_page_avaialble()
    {
        $response = $this->get(route('workflows.index'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function workflow_listed_on_index()
    {
        $workflow = factory(Workflow::class)->create();

        $response = $this->get(route('workflows.index'));

        $response->assertSee(htmlspecialchars($workflow->name, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function create_workflow_available()
    {
        $response = $this->get(route('workflows.create'));
        $response->assertStatus(200)
            ->assertSee('/js/workflow');
    }

    /**
     * @test
     */
    public function workflow_gets_saved()
    {
        Session::start();

        $name = $this->faker->name;


        $data = [
            'goal' => [
                'type' => 'goal',
                'conditions' => [
                    'type' => 'and',
                    'key' => 'App\\Services\\Workflows\\View\\Conditions\\OwnsCourse',
                    'inputs' => [
                        'name' => 'operator',
                        'type' => 'select',
                        'options' => null,
                    ],
                    'values' => [
                        'value' => 1,
                        'title' => 'Inbox BluePrint',
                        'options' => null,
                    ],
                ],
            ],
            'workflow' => [
                [
                    'type' => 'ifelse',
                    'key' => 'Ifelse',
                    'conditions' => [
                        'type' => 'and',
                        'key' => 'App\\Services\\Workflows\\View\\Conditions\\OwnsCourse',
                        'inputs' => [
                            'name' => 'operator',
                            'type' => 'select',
                        ],
                        'values' => [
                            'value' => 6,
                            'title' => 'Inbox BluePrint',
                        ],
                    ],
                    "nodes_false" => [
                        [
                            "key" => "App\Services\Workflows\View\Actions\RemoveFromWorkflow",
                            "value" => null,
                            "type" => "action",
                        ],
                    ],
                    "nodes_true" => [
                        [
                            "key" => "App\Services\Workflows\View\Actions\RemoveFromWorkflow",
                            "value" => null,
                            "type" => "action",
                        ],
                    ],
                ],
            ],
            'name' => $name,
            "details" => [
                "id" => "",
                "name" => "",
            ],
            '_token' => Session::token(),
        ];

        $this->json('POST', route('workflows.store'), $data);

        $this->assertDatabaseHas('workflows', [
            'name' => $name
        ]);
    }

    /**
     * @test
     */
    public function workflow_edit_available()
    {
        $workflow = factory(Workflow::class)->create();
        $response = $this->get(route('workflows.edit', ['workflow' => $workflow]));
        $response->assertStatus(200)
            ->assertSee($workflow->name);
    }

    /**
     * @test
     */
    public function workflow_status_is_updated()
    {
        $workflow = factory(Workflow::class)->create(['status' => 0]);
        $response = $this->post(route('workflows.update-status', ['workflow' => $workflow]));
        $response->assertJson(['OK']);

        $this->assertDatabaseHas('workflows', ['id' => $workflow->id, 'status' => !$workflow->status]);
    }

    public function workflow_validates()
    {

    }
}
