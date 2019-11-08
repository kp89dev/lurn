<?php
namespace App\Services\Sendlane\Api;

use App\Services\Sendlane\Sendlane;

class AbstractApi
{
    /**
     * @type Sendlane
     */
    protected $client;

    public function __construct(Sendlane $client)
    {
        $this->client = $client;
    }
}
