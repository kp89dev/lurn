<?php
namespace App\Services\Tracker\Contracts;

interface RefererParserInterface
{
    public function getMedium();
    public function getSource();
    public function getSearchTerm();
}
