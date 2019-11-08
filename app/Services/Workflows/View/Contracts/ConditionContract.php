<?php

namespace App\Services\Workflows\View\Contracts;


interface ConditionContract
{
    public function getRepresentation();
    public function isValid($data);
}
