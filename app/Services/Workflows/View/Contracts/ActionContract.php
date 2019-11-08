<?php

namespace App\Services\Workflows\View\Contracts;

interface ActionContract
{
    public function getRepresentation();
    public function isValid($data);
}
