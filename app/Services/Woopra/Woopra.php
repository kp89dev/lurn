<?php
namespace App\Services\Woopra;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Woopra
{
    /**
     * @type Client
     */
    private $client;
    private $appId;
    private $secret;
    const URL = 'https://www.woopra.com/rest/3.0/';
    private $website;

    public function __construct(Client $client, $appId, $secret, $website = 'lurn.com')
    {
        $this->client = $client;
        $this->appId  = $appId;
        $this->secret = $secret;
        $this->website = $website;
    }

    public function post($uri, $data)
    {
        $data['website'] = $this->website;

        $request = new Request(
            'POST',
            self::URL . $uri,
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );

        return $this->client->send($request);
    }
}
