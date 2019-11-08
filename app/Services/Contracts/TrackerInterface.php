<?php
namespace App\Services\Contracts;


interface TrackerInterface
{
    public function track($eventName, $args, $push);
    public function identity($identity);
}
