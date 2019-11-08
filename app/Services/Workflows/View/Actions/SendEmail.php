<?php
namespace App\Services\Workflows\View\Actions;

use App\Models\Template;
use App\Services\Workflows\View\Contracts\ActionContract;

class SendEmail implements ActionContract
{

    protected $title = 'Send Email';
    protected $inputs = [
        [
            'name'        => 'template',
            'type'        => 'select',
            'options'     => []
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
        return count($data['value']) == count($this->inputs);
    }

    protected function getInputs()
    {
        $inputs = $this->inputs;
        
        (new Template)->get(['id', 'title'])->each(function($elem) use (&$inputs) {
            $inputs[0]['options'][] = [
                'value' => $elem->id,
                'title' => $elem->title
            ];
        });

        return $inputs;
    }
}
