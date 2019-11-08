<?php
namespace App\Services\Workflows\View\Conditions;

use App\Services\Workflows\View\Contracts\ConditionContract;
use App\Services\Workflows\View\Helpers\TimeUnits;

class TimeSinceLogin implements ConditionContract
{
    public $title = 'Time Since Last Login is bigger than';
    protected $inputs = [
        [
            'name'    => 'amount',
            'type'    => 'number',
            'value'   => null
        ],
        [
            'name'  => 'unit',
            'type'  => 'select',
            'options' =>  TimeUnits::REPRESENTATION
        ]
    ];

    public function getRepresentation()
    {
        return [
            'key'    => static::class,
            'title'  => $this->title,
            'inputs' => $this->inputs
        ];
    }

    public function isValid($data)
    {
        return count($data['values']) == count($this->inputs);
    }
}
