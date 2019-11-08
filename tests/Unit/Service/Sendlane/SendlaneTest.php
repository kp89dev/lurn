<?php
namespace Tests\Unit\Listerners\Account\Normal;

use App\Services\Sendlane\Sendlane;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SendlaneTest extends \TestCase
{
    private $testCredentials = [
        'api'       => 'api',
        'hash'      => 'hash',
        'subdomain' => 'subdomain'
    ];

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function service_throws_exception_when_fields_are_missing()
    {
        $client = \Mockery::mock(Client::class);
        
        new Sendlane($client, []);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function nonexisting_api_throws_error()
    {
        $client = \Mockery::mock(Client::class);

        $client = new Sendlane($client, $this->testCredentials);
        $client->nonexistingApi;
    }

    /**
     * @test
     * @dataProvider dataForApiCall
     */
    public function available_api_gets_called_successfully($property, $method, $methodParams, $expectedResponse)
    {
        $client = \Mockery::mock(Client::class);

        $subdomain = $this->testCredentials['subdomain'];
        $client->shouldReceive('post')->with(
            \Mockery::on(function($argument) use ($subdomain){
                return strpos($argument, $subdomain) !== false;
            }),
            $expectedResponse
        );

        $service = new Sendlane($client, $this->testCredentials);
        $service->$property->$method(...$methodParams);
    }

    public function dataForApiCall()
    {
        return [
            ['lists', 'get', [1, 50], $this->expectedResponse(['page'=> 1, 'limit' => 50, 'list_id' => null])],
            ['subscribers', 'add', ['marius@marius.com', 11], $this->expectedResponse(['email'=> 'marius@marius.com', 'list_id' => 11])],
        ];
    }

    private function expectedResponse($parameters)
    {
        return [
            'form_params' => array_merge(
                $parameters,
                ['api' => $this->testCredentials['api']],
                ['hash' => $this->testCredentials['hash']]
            )
        ];
    }
}
