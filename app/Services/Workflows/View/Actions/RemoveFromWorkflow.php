<?php
namespace App\Services\Workflows\View\Actions;

use App\Models\Workflows\Workflow;
use App\Services\Workflows\View\Contracts\ActionContract;

class RemoveFromWorkflow implements ActionContract
{
    protected $title = 'Remove from workflow';
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
        return true;
    }

    private function getInputs()
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
