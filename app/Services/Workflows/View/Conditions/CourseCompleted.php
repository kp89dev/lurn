<?php
namespace App\Services\Workflows\View\Conditions;

use App\Models\Course;
use App\Services\Workflows\View\Contracts\ConditionContract;

class CourseCompleted implements ConditionContract
{
    public $title = 'Completed Course';
    protected $inputs = [
        [
            'name'    => 'completed_course',
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
        $courses = (new Course)->orderBy('created_at', 'DESC')->get(['title']);

        $courses->each(function($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value'   => $elem->title,
                'title' => $elem->title
            ];
        });

        return $inputs;
    }
}
