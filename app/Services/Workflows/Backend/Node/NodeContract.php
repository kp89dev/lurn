<?php


namespace App\Services\Workflows\Backend\Node;


use App\Models\Workflows\UserWorkflow;

interface NodeContract
{
    public function execute(UserWorkflow $userWorkflow, array $node);
}
