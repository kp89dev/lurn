<?php
namespace App\Services\Workflows\View\Conditions;

use App\Models\UserLogin;
use App\Services\Workflows\View\Contracts\ConditionContract;

class Country implements ConditionContract
{
    public $title = 'Country';
    protected $inputs = [
        [
            'name'    => 'location',
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

    public function getInputs()
    {
        $inputs = $this->inputs;
        $campaigns = UserLogin::groupBy('country')
            ->orderBy('country', 'ASC')
            ->get(['country']);

        $campaigns->each(function($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value'   => $elem->country,
                'title' => $elem->country
            ];
        });

        return $inputs;
    }
}
