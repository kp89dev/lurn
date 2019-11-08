<?php
namespace App\Services\Sendlane;

use GuzzleHttp\Client;
use Psr\Log\InvalidArgumentException;

class Sendlane
{
    private $apis = [];
    private $sendlaneCredentials = [];
    private $client;

    public function __construct(Client $client, array $sendlaneCredentials)
    {
        if (
            ! array_key_exists('api', $sendlaneCredentials) ||
            ! array_key_exists('hash', $sendlaneCredentials) ||
            ! array_key_exists('subdomain', $sendlaneCredentials)
        ) {
            throw new InvalidArgumentException("Sendlane credentials must contain api, hash and domain");
        }

        $this->sendlaneCredentials = $sendlaneCredentials;
        $this->client = $client;
    }

    public function request($endpoint, $parameters)
    {
        return $this->client->post(
            $this->getCallableUrl($endpoint),
            $this->getPayload($parameters)
        );
    }

    public function __get(string $name)
    {
        if (class_exists($this->getFullyQualifiedClassName($name))) {
            return $this->getApi($name);
        }

        throw new \InvalidArgumentException("Property not defined. Please define the API before using it");
    }

    private function getApi($class)
    {
        $class = $this->getFullyQualifiedClassName($class);

        if (! array_key_exists($class, $this->apis)) {
            $this->apis[$class] = new $class($this);
        }

        return $this->apis[$class];
    }

    private function getFullyQualifiedClassName($name)
    {
        return '\App\Services\Sendlane\Api\\' . ucfirst($name);
    }

    private function getCallableUrl($endpoint)
    {
        return 'https://' . $this->sendlaneCredentials['subdomain'] . '.sendlane.com' . $endpoint;
    }

    private function getPayload($parameters)
    {
        return [
            'form_params' => array_merge(
                $parameters,
                ['api' => $this->sendlaneCredentials['api']],
                ['hash' => $this->sendlaneCredentials['hash']]
            )
        ];
    }
}
