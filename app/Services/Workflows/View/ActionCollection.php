<?php
namespace App\Services\Workflows\View;

use App\Services\Workflows\View\Actions\RemoveFromWorkflow;
use App\Services\Workflows\View\Actions\SendEmail;
use App\Services\Workflows\View\Actions\AddToWorkflow;
use App\Services\Workflows\View\Contracts\ActionContract;

class ActionCollection implements ActionContract
{
    const ACTIONS = [
        SendEmail::class,
        AddToWorkflow::class,
        RemoveFromWorkflow::class
    ];

    public function getRepresentation()
    {
        $results = [];
        foreach (self::ACTIONS as $condition) {
            $object = new $condition;

            array_push($results, $object->getRepresentation());
        }

        return $results;
    }

    public function isValid($data)
    {
        $comparator = new $data['key'];

        if (! $comparator->isValid($data)) {
            return false;
        }

        return true;
    }
}
