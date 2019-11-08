<?php
namespace App\Providers;

use App\Services\Woopra\Woopra;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\ServiceProvider;
use App\Services\Logger\Cloudwatch as CloudwatchLogger;

class WoopraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Woopra::class, function ($app) {
            $logger = $app->makeWith(CloudwatchLogger::class, ['streamName' => 'woopra']);

            $stack = HandlerStack::create();
            $stack->push(
                Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG))
            );

            $appId  = env('WOOPRA_APP_ID', 'PQRSSS1APB87SM6364X91GSYU4GOAIMX');
            $secret = env('WOOPRA_KEY', 'xjEdlhScnccRgi9b852DuNpFMvRern028GeBtBfwzOqcAnpzgIYdDSthAuwFNqaj');

            $client = new Client([
                'auth'    => [$appId, $secret],
                'handler' => $stack
            ]);

            return new Woopra($client, $appId, $secret);
        });
    }
}
