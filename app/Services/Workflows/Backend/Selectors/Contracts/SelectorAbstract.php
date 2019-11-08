<?php
namespace App\Services\Workflows\Backend\Selectors\Contracts;

abstract class SelectorAbstract
{
    protected $name = "";

    public function __construct()
    {
        $this->name = class_basename($this) . '_' . bin2hex(random_bytes(4));
    }

    /**
     * @param $tablePrefix
     */
    protected function getAlias($tablePrefix)
    {
        return $tablePrefix . '_' . $this->name;
    }
}
