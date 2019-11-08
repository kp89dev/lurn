<?php
namespace App\Services\Workflows\View\Conditions;

use App\Models\Tracker\Campaign;
use App\Services\Workflows\View\Contracts\ConditionContract;

class CameThroughCampaign implements ConditionContract
{
    public $title = 'Registered Through Campaign';
    protected $inputs = [
        [
            'name'    => 'campaign',
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
        return count($data['values']) == count($this->inputs);
    }

    protected function getInputs()
    {
        $inputs = $this->inputs;
        $campaigns = Campaign::groupBy('name')
                            ->orderBy('created_at', 'DESC')
                            ->get(['name']);

        $campaigns->each(function($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value'   => $elem->name,
                'title' => $elem->name
            ];
        });

        return $inputs;
    }
}
