<?php
namespace App\Services\Workflows\View\Actions;

use App\Services\Workflows\View\Contracts\ActionContract;
use App\Models\Workflows\Workflow;

class AddToWorkflow implements ActionContract
{
    protected $title = 'Add to workflow';
    protected $inputs = [
        [
            'name'    => 'workflow',
            'type'    => 'select',
            'options' => []
        ]
    ];

    public function getRepresentation()
    {
        return [
            'key'    => static::class,
            'title'  => $this->title,
            'inputs' => $this->getInputs()
        ];
    }

    public function isValid($data)
    {
        return (! empty($data['value']));
    }

    protected function getInputs()
    {
        $inputs = $this->inputs;
        $workflows = Workflow::where('status', 1)
                        ->orderBy('id', 'DESC')
                        ->get(['id', 'name']);

        $workflows->each(function($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value'   => $elem->id,
                'title' => $elem->name
            ];
        });

        return $inputs;
    }
}
