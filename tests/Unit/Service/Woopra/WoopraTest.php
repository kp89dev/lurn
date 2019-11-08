<?php
namespace Tests\Unit\Service\Woopra;

use App\Services\Woopra\Woopra;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class WoopraTest extends \TestCase
{
    /**
     * @test
     */
    public function woopra_service_can_be_called()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
               ->method('send')
               ->with(self::isInstanceOf(Request::class));

        $service = new Woopra($client, 'appId', 'secret', 'lurn.com');
        $service->post('test', []);
    }
}
