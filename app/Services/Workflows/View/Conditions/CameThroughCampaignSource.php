<?php
namespace App\Services\Workflows\View\Conditions;

use App\Models\Tracker\Campaign;
use App\Services\Workflows\View\Contracts\ConditionContract;

class CameThroughCampaignSource  implements ConditionContract
{
    public $title = 'Registered Through Campaign Source';
    protected $inputs = [
        [
            'name'    => 'campaign_source',
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
        $campaigns = Campaign::groupBy('source')
            ->whereNotNull('source')
            ->orderBy('created_at', 'DESC')
            ->groupBy('source')
            ->get(['source']);

        $campaigns->each(function ($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value' => $elem->source,
                'title' => $elem->source
            ];
        });

        return $inputs;
    }
}
