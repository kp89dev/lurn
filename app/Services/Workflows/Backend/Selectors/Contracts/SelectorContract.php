<?php

namespace App\Services\Workflows\Backend\Selectors\Contracts;


interface SelectorContract
{
    public function join($query, $values);
    public function where($query, $values);
}
