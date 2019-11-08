<?php
namespace App\Services\Workflows\View\Conditions;

use App\Models\Course;
use App\Services\Workflows\View\Contracts\ConditionContract;

class OwnsCourse implements ConditionContract
{
    public $title = 'Owns Course';
    protected $inputs = [
        [
            'name'    => 'operator',
            'type'    => 'select',
            'options' => null
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
        if (count($data['values']) != count($this->inputs)) {
            return false;
        }

        foreach ($data['values'] as $value) {
            if (empty($value['value'])) {
                return false;
            }
        }

        return true;
    }

    private function getInputs()
    {
        $courses = (new Course)
                        ->where('status', 1)
                        ->get(['id', 'title']);

        $inputs  = $this->inputs;
        $courses->each(function ($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value' => $elem->id,
                'title'=> $elem->title
            ];
        });

        return $inputs;
    }
}
