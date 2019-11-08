<?php
namespace App\Services\Workflows\View\Conditions;

use App\Services\Workflows\View\Contracts\ConditionContract;
use App\Services\Workflows\View\Helpers\Operators;

class TotalPurchasedAmount implements ConditionContract
{
    public $title = 'All Time Purchased Amount';
    protected $inputs = [
        [
            'name'    => 'operator',
            'type'    => 'select',
            'options' => Operators::REPRESENTATION
        ],
        [
            'name'  => 'amount',
            'type'  => 'number',
            'value' =>  null
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
